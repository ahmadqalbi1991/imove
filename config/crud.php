 <?php
    return [
        'operations' => [
            'c'     => 'Create',
            'r'     => 'Read',
            'u'     => 'Update',
            'd'     => 'Delete',
        ],
        'site_modules' => [
            'dashboard'     => ['name'=>'Dashboard','operations'=>['r']],
            'users' => ['name'=>'Admin Users','operations'=>['c','r','u','d']],
            'drivers' => ['name'=>'Truck Drivers','operations'=>['c','r','u','d']],
            'customers'     => ['name'=>'Customers','operations'=>['c','r','u','d']],
            'bookings'      => ['name'=>'Bookings','operations'=>['c','r','u','d']],
            'earnings'      => ['name'=>'Earnings','operations'=>['c','r','u','d']],
            'reviews'       => ['name'=>'Reviews','operations'=>['c','r','u','d']],
            'notifications' => ['name'=>'Notifications','operations'=>['c','r','u','d']],
            'reports'       => ['name'=>'Reports','operations'=>['c','r','u','d']],
            'blacklists'       => ['name'=>'BlackLists','operations'=>['c','r','u','d']],
            'pages'           => ['name'=>'CMS','operations'=>['c','r','u','d']],
            'user_roles'    => ['name'=>'User Roles','operations'=>['c','r','u','d']],
            'deligates'     => ['name'=>'Deligates','operations'=>['c','r','u','d']],
            'company'     => ['name'=>'Companies','operations'=>['c','r','u','d']],
            'truck_types'   => ['name'=>'Truck Types','operations'=>['c','r','u','d']],
            'shipping_methods'     => ['name'=>'Shipping Methods','operations'=>['c','r','u','d']],
            'countries'     => ['name'=>'Countries','operations'=>['c','r','u','d']],
            'languages'     => ['name'=>'Languages','operations'=>['c','r','u','d']],
        ]
    ];
?>
