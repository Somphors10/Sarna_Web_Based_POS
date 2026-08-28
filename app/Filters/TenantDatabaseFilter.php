<?php

namespace App\Filters;

use App\Libraries\TenantContext;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Throwable;

class TenantDatabaseFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        try {
            session();
        } catch (Throwable $e) {
            return;
        }

        $path = strtolower(trim((string) uri_string(), '/'));
        $is_platform_route = $path === 'super-admin'
            || str_starts_with($path, 'super-admin/')
            || $path === 'login'
            || str_starts_with($path, 'login/')
            || $path === 'saas'
            || str_starts_with($path, 'saas/')
            || $path === 'register-company'
            || $path === '';

        $context = new TenantContext();

        if ($is_platform_route) {
            $context->restoreSharedConnection();
            return;
        }

        $tenant_id = (int)(session()->get('tenant_id') ?? 0);
        if ($tenant_id > 0) {
            $context->applyRuntimeConnection($tenant_id);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
