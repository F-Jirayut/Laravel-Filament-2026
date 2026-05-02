<?php

namespace App\Http\Middleware;

use App\Models\Menu;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMenuActivation
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. ดึง Path ปัจจุบัน (เช่น admin/menus)
        // ตัดคำว่า admin ออกเพื่อให้ตรงกับที่เก็บในฐานข้อมูล (ถ้าคุณเก็บแบบ /admin/...)
        $currentPath = '/' . $request->path();

        // 2. เช็คในตาราง menus ว่าเส้นทางนี้ถูกสั่งปิด (is_active = 0) อยู่หรือไม่
        $menu = Menu::where('is_active', false)
                ->get()
                ->first(function ($menu) use ($currentPath) {
                    return str_starts_with($currentPath, $menu->route);
                });

        if ($menu && !$menu->is_active) {
            // ถ้าเจอเมนูนี้และ is_active เป็น false ให้ดีดออกทันที
            abort(403, 'เมนูนี้ถูกปิดใช้งานชั่วคราว');
        }

        return $next($request);
    }
}
