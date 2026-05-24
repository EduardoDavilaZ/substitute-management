<?php

/**
 * Transforms flat database result sets into a hierarchical, nested structure.
 * 
 * This utility handles one-to-many and many-to-many relationships by grouping
 * rows by unique identifiers and optionally nesting related data into sub-arrays.
 * It also supports attribute mapping via column prefixes.
 *
 * @param array $rows   The flat array of associative arrays from the database (PDO::FETCH_ASSOC).
 * @param array $config Configuration mapping: [id_column => container_name] 
 *                      or [id_column => ['container' => 'name', 'prefix' => 'prefix_']].
 * @return array        The structured, hierarchical array.
 */
function db_nest(array $rows, array $config): array 
{
    $result = [];

    foreach ($rows as $row) {
        // Pointer to traverse the tree structure
        $current = &$result;

        foreach ($config as $id_column => $options) {
            // Normalize configuration options
            $container = is_array($options) ? ($options['container'] ?? null) : $options;
            $prefix = is_array($options) ? ($options['prefix'] ?? null) : null;

            $id_val = $row[$id_column] ?? null;

            if ($id_val === null) {
                break;
            }

            if (!isset($current[$id_val])) {
                if ($prefix) {
                    $item_data = [];
                    foreach ($row as $key => $value) {
                        if (str_starts_with($key, $prefix)) {
                            $clean_key = str_replace($prefix, '', $key);
                            $item_data[$clean_key] = $value;
                        }
                    }
                    $current[$id_val] = $item_data;
                } else {
                    $item_data = [];
                    $all_prefixes = array_filter(array_column($config, 'prefix'));
                    
                    foreach ($row as $key => $value) {
                        $is_child_data = false;
                        foreach ($all_prefixes as $p) {
                            if (str_starts_with($key, $p)) {
                                $is_child_data = true;
                                break;
                            }
                        }
                        if (!$is_child_data) {
                            $item_data[$key] = $value;
                        }
                    }
                    $current[$id_val] = $item_data;
                }

                if ($container) {
                    $current[$id_val][$container] = [];
                }
            }

            if ($container) {
                $current = &$current[$id_val][$container];
            }
        }
    }

    /**
     * Clean up temporary associative keys to return standard indexed arrays.
     * This ensures the final JSON output is an array [] rather than an object {}.
     */
    $container_list = array_values(array_filter(array_map(function($v) {
        return is_array($v) ? ($v['container'] ?? null) : $v;
    }, $config)));

    return db_reindex($result, $container_list);
}

/**
 * Recursively reindexes an associative array into a numerical indexed array.
 *
 * @param array $data       The nested associative array to reindex.
 * @param array $containers The list of child keys that should also be reindexed.
 * @return array            The cleaned, indexed result set.
 */
function db_reindex(array $data, array $containers): array 
{
    // Convert current level keys to numerical indexes
    $data = array_values($data);
    $current_container = array_shift($containers);
    
    // Recurse into children if a container is specified for this level
    if ($current_container) {
        foreach ($data as &$item) {
            if (isset($item[$current_container])) {
                $item[$current_container] = db_reindex($item[$current_container], $containers);
            }
        }
    }
    
    return $data;
}