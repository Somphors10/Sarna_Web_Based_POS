<?php

/**
 * Does the logged-in user have this CRUD action on the current (or given) module?
 * Rule: permission checked in Role → feature allowed; not checked → not allowed.
 */
function can_crud(string $action, ?string $module = null): bool
{
    if (function_exists('rbac_ui_can')) {
        return rbac_ui_can($action, $module);
    }

    return true;
}

/**
 * @return array{create: bool, read: bool, update: bool, delete: bool}
 */
function crud_flags(?string $module = null): array
{
    if (function_exists('rbac_ui_flags')) {
        return rbac_ui_flags($module);
    }

    return ['create' => true, 'read' => true, 'update' => true, 'delete' => true];
}

/**
 * Normalize ids[] posted from bootstrap-table bulk actions.
 *
 * @param mixed $ids
 * @return list<int>
 */
function normalize_post_ids($ids): array
{
    if ($ids === null || $ids === false || $ids === '') {
        return [];
    }

    if (!is_array($ids)) {
        $ids = [$ids];
    }

    $normalized = [];

    foreach ($ids as $id) {
        if ($id === null || $id === '') {
            continue;
        }

        $id = (int)$id;
        if ($id > 0) {
            $normalized[] = $id;
        }
    }

    return array_values(array_unique($normalized));
}

/**
 * Normalize string ids (e.g. tax codes) from bulk delete actions.
 *
 * @param mixed $ids
 * @return list<string>
 */
function normalize_post_string_ids($ids): array
{
    if ($ids === null || $ids === false || $ids === '') {
        return [];
    }

    if (!is_array($ids)) {
        $ids = [$ids];
    }

    $normalized = [];

    foreach ($ids as $id) {
        $id = trim((string)$id);
        if ($id !== '') {
            $normalized[] = $id;
        }
    }

    return array_values(array_unique($normalized));
}

/**
 * Allowed rows-per-page options for bootstrap-table lists.
 *
 * @return list<int|string>
 */
function table_page_list(): array
{
    return [10, 50, 100, 'all'];
}

/**
 * Resolve configured page size to an allowed list value.
 */
function table_page_size($configured_size = null): int
{
    $allowed = [10, 50, 100];
    $size = (int)($configured_size ?? $allowed[0]);

    return in_array($size, $allowed, true) ? $size : $allowed[0];
}
