<?php

namespace App\Utils;

class GlobalConstant
{
    public const OWNER_OCCUPIED = 'OWNER_OCCUPIED';
    public const NON_OWNER_OCCUPIED = 'NON_OWNER_OCCUPIED';

    // Status
    public const STATUS_ACTIVE = 1;
    public const STATUS_INACTIVE = 0;

    // Pagination
    public const DEFAULT_PAGINATION = 10;

    // Roles
    public const ROLE_ADMIN = 'admin';
    public const ROLE_USER = 'user';

    // Date formats
    public const DATE_FORMAT = 'Y-m-d';
    public const DATETIME_FORMAT = 'Y-m-d H:i:s';

    // UI
    public const FLASH_SUCCESS = 'success';
    public const FLASH_ERROR = 'error';
}
