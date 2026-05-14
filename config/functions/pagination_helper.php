<?php

/**
 * Handles database pagination, searching, and sorting
 */
function getPaginatedData(
    $pdo,             // PDO database connection
    $selectSql,
    $fromWhereSql,    // base FROM and WHERE clause without search conditions
    $searchColumns,   // columns to apply search terms to
    $allowedSort,
    $paginationParams, // ['page' => 1, 'sort' => 'id', 'order' => 'ASC']
    $search = '',
    $params = [] // filter parameters
) {

    // Set pagination limits and calculate offset
    $limit = 10;
    $page = (int)($paginationParams['page'] ?? 1);
    $offset = ($page - 1) * $limit;

    // Validate sorting parameters against allowed list
    $keys = array_keys($allowedSort);
    $fallback = $keys[0];
    $sort = array_key_exists($paginationParams['sort'], $allowedSort) ? $paginationParams['sort'] : $fallback;
    $order = strtolower($paginationParams['order'] ?? 'asc');
    $actualSort = $allowedSort[$sort];

    $searchSql = "";

    // Build the search query if search terms are provided
    if ($search && !empty($searchColumns)) {
        $searchConditions = [];
        // Use indexed parameter names to avoid conflicts when multiple search columns are used
        foreach ($searchColumns as $index => $column) {
            $paramName = ":search" . $index;
            // $searchConditions: name LIKE :search0 OR email LIKE :search1
            $searchConditions[] = "$column LIKE $paramName";
            $params[$paramName] = "%$search%";
        }

        $connector = stripos($fromWhereSql, 'WHERE') !== false ? ' AND ' : ' WHERE ';
        $searchSql = $connector . "(" . implode(' OR ', $searchConditions) . ")";
    }

    // Query 1: Get the total number of records for pagination math
    $countQuery = "SELECT COUNT(*) " . $fromWhereSql . $searchSql;
    $stmtCount = $pdo->prepare($countQuery);
    $stmtCount->execute($params);
    $totalResults = $stmtCount->fetchColumn();
    $totalPages = ceil($totalResults / $limit);

    // Query 2: Get the actual rows for the current page
    $dataQuery = "$selectSql $fromWhereSql $searchSql ORDER BY $actualSort $order LIMIT $limit OFFSET $offset";
    // print_r($dataQuery);
    // print_r($params);
    // die();

    $stmt = $pdo->prepare($dataQuery);
    $stmt->execute($params);
    $data = $stmt->fetchAll();

    // Return results and metadata
    return [
        'data'          => $data,
        'total_pages'   => $totalPages,
        'current_page'  => $page,
        'current_sort'  => $sort,
        'current_order' => $order,
        'total_results' => $totalResults
    ];
}

/**
 * Generates a URL for table headers to toggle sorting directions
 */
function getSortURL($column, $current_sort, $current_order)
{
    // Switch direction if the same column is clicked again
    $new_order = (strtolower($column) == strtolower($current_sort) && strtolower($current_order) == 'asc') ? 'desc' : 'asc';
    $params = $_GET;
    $params['sort'] = $column;
    $params['order'] = $new_order;

    // Reset to page 1 when changing sort order
    $params['page'] = 1;
    return "?" . http_build_query($params);
}

/**
 * Renders pagination controls based on total pages and current page
 */
function renderPagination($totalPages, $currentPage)
{
    if ($totalPages <= 1) return '';

    $html = '<nav aria-label="Page navigation"><ul class="pagination justify-content-center">';

    for ($i = 1; $i <= $totalPages; $i++) {
        $params = $_GET;
        $params['page'] = $i;
        $link = "?" . http_build_query($params);
        $active = ($i == $currentPage) ? 'active' : '';

        $html .= "<li class='page-item {$active}'>";
        $html .= "<a class='page-link' href='{$link}'>{$i}</a>";
        $html .= "</li>";
    }

    $html .= '</ul></nav>';
    return $html;
}

/**
 * Renders a sortable table header with appropriate icons for sorting direction
 */
function renderSortHeader($label, $column, $currentSort, $currentOrder)
{
    $url = getSortURL($column, $currentSort, $currentOrder);
    $icon = '';
    if ($column == $currentSort) {
        $icon = (strtolower($currentOrder) == 'asc') ? ' ↑' : ' ↓';
    } else {
        $icon = ' ↕';
    }
    return "<th><a href='{$url}' style='text-decoration:none; color:inherit;'>{$label}{$icon}</a></th>";
}
