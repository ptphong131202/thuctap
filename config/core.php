<?php

/**
 * Các config của core
 */
return [
    'app_version' => '2.2.24',
    /**
     * Default user
     */
    'super_admin' => [
        'user_id' => 1010,
        'username' => 'admin',
        'email' => 'admin@gmail.com',
        'password' => '123456',
        'name' => 'Administrators',
        'status' => 1,
        'type' => 1,
        'canbo' => [
            'cb_ma' => 'admin',
            'cb_ho' => null,
            'cb_ten' => 'Administrators',
            'cb_gioitinh' => 1,
        ]
    ],

    /**
     * Default item in page
     */
    'page_size' => 10,

    /**
     * Navigation
     */
    'menu_groups' => [
        [
            'group_name' => 'menu.main_navigation',
            'childs' => [
                [
                    'name' => 'menu.dashboard',
                    'route' => 'dashboard',
                    'icon' => 'fa fa-dashboard'
                ],
                [
                    'name' => 'Danh mục',
                    'icon' => 'fa fa-th-list',
                    'permission' => 'admin.index',
                    'childs' => [
                        // [
                        // 	'name' => 'Hệ đào tạo',
                        // 	'route' => 'he-dao-tao.index',
                        // 	'icon' => 'fa fa-sticky-note-o',
                        // 	'permission' => 'admin.index',
                        // ],
                        [
                            'name' => 'Niên khóa',
                            'route' => 'nien-khoa.index',
                            'icon' => 'fa fa-sticky-note-o',
                            'permission' => 'admin.index',
                        ],
                        [
                            'name' => 'Ngành nghề đào tạo',
                            'route' => 'nganh-nghe.index',
                            'icon' => 'fa fa-sticky-note-o',
                            'permission' => 'admin.index',
                        ],
                        [
                            'name' => 'Chương trình đào tạo',
                            'route' => 'khoa-dao-tao.index',
                            'icon' => 'fa fa-sticky-note-o',
                            'permission' => 'admin.index',
                        ],
                        [
                            'name' => 'Môn học',
                            'route' => 'mon-hoc.index',
                            'icon' => 'fa fa-sticky-note-o',
                            'permission' => 'admin.index',
                        ],
                        [
                            'name' => 'Quyết định',
                            'route' => 'quyet-dinh.index',
                            'icon' => 'fa fa-sticky-note-o',
                            'permission' => 'posts',
                        ],
                    ]
                ],
                [
                    'name' => 'Quản lý lớp học',
                    'icon' => 'fa fa-child',
                    'permission' => 'admin.index',
                    'route' => 'lop-hoc.index',
                ],
                [
                    'name' => 'Quản lý điểm',
                    'icon' => 'fa fa-book',
                    'permission' => 'admin.score,xemdiem,nhaprenluyen',
                    'route' => 'nhap-diem.index',
                ],
                [
                    'name' => 'Đợt thi tốt nghiệp',
                    'route' => 'dot-thi.index',
                    'icon' => 'fa fa-book',
                    'permission' => 'admin.index,admin.thitotnghiep',
                ],
                [
                    'name' => 'Đợt xét tốt nghiệp',
                    'route' => 'dot-xet-tot-nghiep.index',
                    'icon' => 'fa fa-graduation-cap',
                    'permission' => 'admin.index,admin.xettotnghiep',
                ],
                [
                    'name' => 'Nhật ký sinh viên',
                    'route' => 'nhat-ky.index',
                    'icon' => 'fa fa-sticky-note-o',
                    'permission' => 'admin.index,admin.xettotnghiep',
                ],
                [
                    'name' => 'Tra cứu điểm',
                    'route' => 'tra-cuu-diem.tra-cuu',
                    'permission' => 'student',
                    'icon' => 'fa fa-search',
                ],
                [
                    'name' => 'Quản trị',
                    'icon' => 'fa fa-cogs',
                    'permission' => 'admin.users',
                    'childs' => [
                        [
                            'name' => 'Người dùng',
                            'route' => 'user.index',
                            'icon' => 'fa fa-sticky-note-o',
                            'permission' => 'admin.users',
                        ],
                        [
                            'name' => 'Kiểm tra dữ liệu',
                            'route' => 'nhap-diem.kiem-tra',
                            'icon' => 'fa fa-sticky-note-o',
                            'permission' => 'admin.index',
                        ],
                        [
                            'name' => 'Nhật ký chỉnh sửa điểm',
                            'route' => 'nhap-diem.nhat-ky',
                            'icon' => 'fa fa-sticky-note-o',
                            'permission' => 'admin.index',
                        ],
                    ]
                ]
            ]
        ],
    ],

    'theme' => [
        'admin' => 'layouts.admin',
    ],

    /**
     * Ảnh mặc định của bài viết
     */
    'default' => [
        'user' => [
            'avatar' => '/images/default/user_avatar.png'
        ],
        'post' => [
            'editor' => 'tinymce'
        ]
    ],

    /**
     * Các route điều hướng mặc định
     */
    'routes' => [
        'admin' => [
            'dashboard' => 'dashboard',
        ],
        'blog' => [
            //
        ],
    ],
];
