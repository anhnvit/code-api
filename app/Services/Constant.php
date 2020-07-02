<?php
/**
 * Created by PhpStorm.
 * User: anhnv
 * Date: 3/26/2020
 * Time: 10:58 AM
 */

namespace App\Services;


class Constant
{
    const SERVICE_CODE = "VNPMEWEATHER";
    const SEND_MT = "Send_mt";
    const SUB_TRANSACTION_DETAILS = "Sub_transaction_details";

    const VAS_STATUS_SUCCESS = 200;
    const STATUS_SUCCESS = 0;
    const STATUS_ERROR = 1;
    const STATUS_ERROR_SESSION = -1;
    const STATUS_ERROR_NO_EXIT = 2;
    const STATUS_ERROR_SERVER = 500;
    const STATUS_ERROR_API_VAS = 600;
    const STATUS_ERROR_AUTH = 401;
    const ARRAY_MESSAGE_SUCCESS = [
        'success' => "Thành công",
        'logout_success' => 'Đăng xuất thành công',
        'chang_password_success' => "Đổi mật khẩu thành công",
        'update_user_info_success' => "Cập nhật thông tin thành công",
        'login_success' => "Đăng nhập thành công",

    ];
    const ARRAY_MESSAGE_ERROR = [
        1 => "Tài khoản đã tồn tại",
        2 => "Tài khoản chưa đăng ký dịch vụ",
        500 => "Lỗi Server",
        600 => "Lỗi gọi Api Vas",
        'error' => "Thất bại",
        'logout_error' => 'Đăng xuất thất bại',
        'chang_password_error' => "Đổi mật khẩu thất bại",
        'update_user_info_error' => "Cập nhật thông tin thất bại",
        'session_not_exit' => "Tài khoản đăng nhập trên thiết bị khác",
        'msisdn_error' => "Số điện thoại không đúng",
        'password_error' => 'Mật khẩu đăng nhập không đúng',
        'authenticate' => 'Access denied'
    ];

    const LOGIN_3G = "3G";
    const LOGIN_WIFI = "WIFI";
    /**
     * Trạng thái gói cước
     */
    const STATUS_ACTIVE = 1;
    const STATUS_CANCEL = 0;

    const PACKAGE_INFO = [
        "name" => "Gói VIP",
        "info" => "Cung cấp thông tin thời tiết chuyên sâu hàng ngày. Ngoài ra khách hàng còn được cập nhật và cảnh báo sớm tình hình thiên tai (bão, lũ, hạn hán) khi có sự kiện thời tiết đặc biệt (mưa, dông, cảnh báo bão...), cung cấp thông tin dự báo/cảnh báo thời tiết chi tiết đến từng khu vực tỉnh/thành phố",
        "price" => "1.000đ/ngày",
        "reg_keyword" => "V",
        "reg_shortcode" => "1095",
        "unreg_keyword" => "HUY VIP",
        "unreg_shortcode" => "1095",
        "package_code" => "VIP",
    ];


}