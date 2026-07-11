<aside id="sidebar-wrapper">
    <div class="sidebar-brand sidebar-sticky sidebar-bottom-padding h-auto line-height-0 padding-bottom-zero">
        <a class="navbar-brand d-flex align-items-start justify-content-start py-3 px-2"
            href="{{ route('redirect.login') }}">
            <img class="navbar-brand-full " src="{{ asset('img/logo.png') }}" style="width: 60%;margin-left:5%;"
                class="image-responsive" alt="">
            &nbsp;&nbsp;
            {{-- <span class="navbar-brand-full-name text-black text-wrap w-75">{{ getAppName() }}</span> --}}
        </a>
        {{-- <div class="input-group sidebar-search-box">
            <input type="text" class="form-control searchTerm" id="searchText"
                placeholder="{{ __('messages.placeholder.search_menu') }}">
            <div class="input-group-append sGroup">
                <div class="input-group-text">
                    <i class="fas fa-search search-sign"></i>
                    <i class="fas fa-times close-sign"></i>
                </div>
            </div>
            <div class="no-results mt-3 ml-1">{{ __('messages.no_matching_records_found') }}</div>
        </div> --}}
    </div>
    <div class="sidebar-brand sidebar-brand-sm">
        <a href="{{ route('redirect.login') }}" class="small text-white">
            <img class="navbar-brand-full" src="{{ asset('img/logo.png') }}" style="width: 95%;margin-left:5%;"
                alt="">
        </a>
    </div>

    <ul class="sidebar-menu">
        {{-- <li class="menu-header side-menus">{{ __('messages.dashboard') }}</li> --}}
        <li class="side-menus {{ Request::is('admin/dashboard*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('dashboard') }}"><i class="fas fa-lg fa-tachometer-alt"></i>
                <span class="menu-text-wrap">{{ __('messages.dashboard') }}</span></a>
        </li>
        @canany(['manage_payments', 'manage_credit_notes', 'export_project_invoices', 'update_project_invoices',
            'view_project_invoices', 'view_quotations', 'create_quotations', 'update_quotations', 'delete_quotations',
            'export_quotations', 'view_invoices', 'create_invoices', 'update_invoices', 'delete_invoices',
            'export_invoices', 'view_credit_notes', 'create_credit_notes', 'update_credit_notes', 'delete_credit_notes',
            'export_credit_notes', 'view_customers', 'create_customers', 'update_customers', 'delete_customers',
            'view_manual_sales', 'create_manual_sales', 'update_manual_sales', 'delete_manual_sales'])
            {{-- <li class="menu-header side-menus">{{ __('messages.sales') }}</li> --}}
            <li class="nav-item dropdown side-menus">
                <a class="nav-link has-dropdown" href="#"><i class="fab fa-lg fa-speakap"></i>
                    <span>{{ __('messages.sales') }}</span></a>
                <ul class="dropdown-menu side-menus">
                    @canany(['view_customers', 'create_customers', 'update_customers', 'delete_customers'])
                        <li
                            class="side-menus {{ Request::is('admin/customers*') || Request::is('admin/contacts*') ? 'active  submenu' : '' }}">
                            <a class="nav-link" href="{{ route('customers.index') }}">
                                <i class="fas fa-lg fa-street-view"></i><span
                                    class="menu-text-wrap">{{ __('messages.customers') }}</span></a>
                        </li>
                    @endcanany
                    @canany(['view_quotations', 'create_quotations', 'update_quotations', 'delete_quotations',
                        'export_quotations'])
                        <li class="side-menus {{ Request::is('admin/estimates*') ? 'active submenu' : '' }}">
                            <a href="{{ route('estimates.index') }}"><i class="fas fa-lg fa-calculator"></i>
                                <span class="menu-text-wrap">{{ __('messages.contact.estimates') }}</span>
                            </a>
                        </li>
                    @endcanany
                    @canany(['view_invoices', 'create_invoices', 'update_invoices', 'delete_invoices', 'export_invoices'])
                        <li class="side-menus {{ Request::is('admin/invoices*') ? 'active submenu' : '' }}">
                            <a href="{{ route('invoices.index') }}"><i class="fas fa-lg fa-file-invoice"></i>
                                <span class="menu-text-wrap">{{ __('messages.invoices') }}</span>
                            </a>
                        </li>
                    @endcanany
                    {{-- @canany(['view_project_invoices', 'update_project_invoices', 'export_project_invoices'])
                        <li class="side-menus {{ Request::is('admin/project-invoices*') ? 'active' : '' }}">
                            <a href="{{ route('project-invoices.index') }}"><i class="fas fa-lg fa-file-invoice"></i>
                                <span class="menu-text-wrap">{{ __('messages.invoices') }}</span>
                            </a>
                        </li>
                    @endcanany --}}
                    @canany(['view_credit_notes', 'create_credit_notes', 'update_credit_notes', 'delete_credit_notes',
                        'export_credit_notes'])
                        <li class="side-menus {{ Request::is('admin/credit-notes*') ? 'active  submenu' : '' }}">
                            <a href="{{ route('credit-notes.index') }}"><i class="fas fa-lg fa-clipboard"></i>
                                <span class="menu-text-wrap">{{ __('messages.credit_notes') }}</span>
                            </a>
                        </li>
                    @endcanany
                    @can('manage_proposals')
                        <li class="side-menus {{ Request::is('admin/proposals*') ? 'active submenu' : '' }}">
                            <a href="{{ route('proposals.index') }}"><i class="fas fa-lg fa-scroll"></i>
                                <span class="menu-text-wrap">{{ __('messages.proposals') }}</span>
                            </a>
                        </li>
                    @endcan

                    @can('manage_payment_mode')
                        <li class="side-menus {{ Request::is('admin/payments-list*') ? 'active  submenu' : '' }}">
                            <a href="{{ route('payments.list.index') }}"><i class="fas fa-lg fa-money-check-alt"></i>
                                <span class="menu-text-wrap">{{ __('messages.invoice.invoice_payments') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('view_statement')
                        <li class="side-menus {{ Request::is('admin/customer-statement*') ? 'active  submenu' : '' }}">
                            <a href="{{ route('customer-statement.index') }}"><i class="fas fa-lg fa-receipt"></i>
                                <span class="menu-text-wrap">{{ __('messages.customer_statements.name') }}</span>
                            </a>
                        </li>
                    @endcan
                    @canany(['view_manual_sales', 'create_manual_sales', 'update_manual_sales', 'delete_manual_sales'])
                        <li class="side-menus {{ Request::is('admin/manual-sales*') ? 'active submenu' : '' }}">
                            <a href="{{ route('manual-sales.index') }}"><i class="fas fa-lg fa-file-invoice"></i>
                                <span class="menu-text-wrap">Manual Invoice</span>
                            </a>
                        </li>
                    @endcanany
                </ul>
            </li>
        @endcanany
        {{--   Service start     --}}
        @canany(['view_service_categories', 'create_service_categories', 'update_service_categories',
            'delete_service_categories', 'view_services', 'create_services', 'update_services', 'update_services'])
            <li class="nav-item dropdown side-menus">
                <a class="nav-link has-dropdown" href="#"><i class="fas fa-lg fa-industry"></i>
                    <span>{{ __('messages.service.menu') }}</span>
                </a>
                <ul class="dropdown-menu side-menus">
                    @canany(['view_service_categories', 'create_service_categories', 'update_service_categories',
                        'delete_service_categories'])
                        <li class="side-menus {{ Request::is('admin/service_categories*') ? 'active   submenu' : '' }}">
                            <a href="{{ route('service_categories.index') }}"><i class="fas fa-lg fa-bars"></i>
                                <span class="menu-text-wrap">{{ __('messages.service_categories.manu') }}</span></a>
                        </li>
                    @endcanany
                    @canany(['view_services', 'create_services', 'update_services', 'delete_services'])
                        <li class="side-menus {{ Request::is('admin/products*') ? 'active   submenu' : '' }}">
                            <a href="{{ route('products.index') }}"><i class="fas fa-lg fa-industry"></i>
                                <span class="menu-text-wrap">{{ __('messages.products.products') }}</span>
                            </a>
                        </li>
                    @endcanany

                </ul>
            </li>
        @endcanany


        @canany(['view_purchase_orders', 'create_purchase_orders', 'update_purchase_orders', 'delete_purchase_orders',
            'view_purchase_invoices', 'create_purchase_invoices', 'edit_purchase_invoices', 'delete_purchase_invoices',
            'view_purchase_returns', 'create_purchase_returns', 'edit_purchase_returns', 'delete_purchase_returns',
            'manage_payment_mode'])
            <li class="nav-item dropdown side-menus">
                <a class="nav-link has-dropdown" href="#"><i class="fas fa-lg fa-shopping-cart"></i>
                    <span>{{ __('messages.common.purchase') }}</span>
                </a>
                <ul class="dropdown-menu side-menus">

                    @canany(['view_purchase_orders', 'create_purchase_orders', 'update_purchase_orders',
                        'delete_purchase_orders'])
                        <li class="side-menus {{ Request::is('admin/purchase-orders*') ? 'active submenu' : '' }}">
                            <a href="{{ route('purchase-orders.index') }}"><i class="fas fa-lg fa-calculator"></i>
                                <span class="menu-text-wrap">{{ __('messages.purchase-orders.short_name') }}</span>
                            </a>
                        </li>
                    @endcanany
                    @canany(['view_purchase_invoices', 'create_purchase_invoices', 'edit_purchase_invoices',
                        'delete_purchase_invoices'])
                        <li class="side-menus {{ Request::is('admin/purchase-invoices*') ? 'active submenu' : '' }}">
                            <a href="{{ route('purchase-invoices.index') }}"><i class="fas fa-lg fa-file-invoice"></i>
                                <span class="menu-text-wrap">{{ __('messages.invoices') }}</span></a>
                            </a>
                        </li>
                    @endcanany

                    @can('manage_payment_mode')
                        <li
                            class="side-menus {{ Request::is('admin/purchase-invoice-payments-list*') ? 'active  submenu' : '' }}">
                            <a href="{{ route('purchase-invoice-payments.list.index') }}"><i
                                    class="fas fa-lg fa-money-check-alt"></i>
                                <span class="menu-text-wrap">{{ __('messages.invoice.invoice_payments') }}</span>
                            </a>
                        </li>
                    @endcan

                    @canany(['view_purchase_returns', 'create_purchase_returns', 'edit_purchase_returns',
                        'delete_purchase_returns'])
                        <li class="side-menus  {{ Request::is('admin/purchase-returns*') ? 'active submenu' : '' }}">
                            <a href="{{ route('purchase-returns.index') }}"> <i class="fas fa-lg fa-reply"></i>
                                <span class="menu-text-wrap">{{ __('messages.returns') }}</span></a>
                        </li>
                    @endcanany


                </ul>
            </li>
        @endcanany

        @canany([
            'view_purchase_groups',
            'create_purchase_groups',
            'delete_purchase_groups',
            'view_purchase_categories',
            'create_purchase_categories',
            'delete_purchase_categories',
            'view_purchase_sub_categories',
            'create_purchase_sub_categories',
            'delete_purchase_sub_categories',
            'view_product_units',
            'create_product_units',
            'update_product_units',
            'delete_product_units',
            'view_product_brands',
            'create_product_brands',
            'update_product_brands',
            'delete_product_brands',
            'view_product_sizes',
            'update_product_sizes',
            'create_product_sizes',
            'delete_product_sizes',
            'view_product_colors',
            'update_product_colors',
            'create_product_colors',
            'delete_product_colors',
            'view_purchase_items',
            'create_purchase_items',
            'delete_purchase_items',
            'manage_print_labels',
            'view_opening_stocks',
            'create_opening_stocks',
            'update_opening_stocks',
            'delete_opening_stocks',
            'manage_stock_reports',
            'manage_print_labels',
            'view_product_brands',
            'create_product_brands',
            'update_product_brands',
            'delete_product_brands',
            ])
            <li class="nav-item dropdown side-menus">
                <a class="nav-link has-dropdown" href="#"><i class="fas fa-lg fa-warehouse"></i>
                    <span>{{ __('messages.common.inventory') }}</span>
                </a>
                <ul class="dropdown-menu side-menus">

                    @canany(['view_purchase_groups', 'create_purchase_groups', 'update_purchase_groups',
                        'delete_purchase_groups'])
                        <li class="side-menus  {{ Request::is('admin/purchase-groups*') ? 'active submenu' : '' }}">
                            <a href="{{ route('purchase-groups.index') }}"><i class="fas fa-lg fa-users"></i>
                                <span class="menu-text-wrap">{{ __('messages.common.groups') }}</span></a>
                        </li>
                    @endcanany
                    @canany(['view_purchase_categories', 'create_purchase_categories', 'update_purchase_categories',
                        'delete_purchase_categories'])
                        <li class="side-menus  {{ Request::is('admin/purchase-categories*') ? 'active submenu' : '' }}">
                            <a href="{{ route('purchase-categories.index') }}"><i class="fas fa-lg fa-cash-register"></i>
                                <span class="menu-text-wrap">{{ __('messages.common.categories') }}</span></a>
                        </li>
                    @endcanany
                    @canany(['view_purchase_sub_categories', 'create_purchase_sub_categories',
                        'update_purchase_sub_categories', 'delete_purchase_sub_categories'])
                        <li class="side-menus  {{ Request::is('admin/purchase-sub-categories*') ? 'active submenu' : '' }}">
                            <a href="{{ route('purchase-sub-categories.index') }}"><i class="fas fa-lg fa-stream"></i>
                                <span class="menu-text-wrap">{{ __('messages.common.sub_categories') }}</span></a>
                        </li>
                    @endcanany
                    @canany(['view_product_units', 'create_product_units', 'update_product_units', 'delete_product_units'])
                        <li class="side-menus  {{ Request::is('admin/product-unit*') ? 'active submenu' : '' }}">
                            <a href="{{ route('products.unit.index') }}"><i class="fas fa-lg fa-balance-scale"></i>
                                <span class="menu-text-wrap">{{ __('messages.products.unit') }}</span></a>
                        </li>
                    @endcanany
                    @canany(['view_product_brands', 'create_product_brands', 'update_product_brands',
                        'delete_product_brands'])
                        <li class="side-menus {{ Request::is('admin/product-brand*') ? 'active submenu' : '' }}">
                            <a href="{{ route('products.brand.index') }}"><i class="fas fa-lg fa-tags"></i>
                                <span class="menu-text-wrap">{{ __('messages.products.brand') }}</span></a>
                        </li>
                    @endcanany

                    @canany(['view_product_sizes', 'create_product_sizes', 'update_product_sizes', 'delete_product_sizes'])
                        <li class="side-menus {{ Request::is('admin/product-size*') ? 'active submenu' : '' }}">
                            <a href="{{ route('products.size.index') }}"><i class="fas fa-lg fa-cogs"></i>
                                <span class="menu-text-wrap">{{ __('messages.products.size') }}</span></a>
                        </li>
                    @endcanany

                    @canany(['view_product_colors', 'create_product_colors', 'update_product_colors',
                        'delete_product_colors'])
                        <li class="side-menus {{ Request::is('admin/product-color*') ? 'active submenu' : '' }}">
                            <a href="{{ route('products.color.index') }}"><i class="fas fa-fill-drip"></i>
                                <span class="menu-text-wrap">{{ __('messages.products.color') }}</span></a>
                        </li>
                    @endcanany
                    @canany(['view_purchase_items', 'create_purchase_items', 'update_purchase_items',
                        'delete_purchase_items'])
                        <li class="side-menus  {{ Request::is('admin/purchase-items*') ? 'active submenu' : '' }}">
                            <a href="{{ route('purchase-items.index') }}"><i class="fas fa-lg fa-box"></i>

                                <span class="menu-text-wrap">{{ __('messages.common.items') }}</span></a>
                        </li>
                    @endcanany
                    @canany(['view_purchase_items', 'create_purchase_items', 'update_purchase_items',
                        'delete_purchase_items'])
                        <li class="side-menus  {{ Request::is('admin/item-listing*') ? 'active submenu' : '' }}">
                            <a href="{{ route('item-listing.index') }}"><i class="fas fa-lg fa-list"></i>

                                <span class="menu-text-wrap">Item Listing</span></a>
                        </li>
                    @endcanany
                    @canany(['manage_stock_reports'])
                        <li class="side-menus  {{ Request::is('admin/stock-reports*') ? 'active submenu' : '' }}">
                            <a href="{{ route('stock-reports.index') }}"><i class="fas fa-lg fa-bars"></i>

                                <span class="menu-text-wrap">{{ __('messages.common.stock_reports') }}</span></a>
                        </li>
                    @endcanany
                    @canany(['manage_print_labels'])
                        <li class="side-menus  {{ Request::is('admin/print-labels*') ? 'active submenu' : '' }}">
                            <a href="{{ route('print-labels.index') }}"><i class="fas fa-lg fa-print"></i>

                                <span class="menu-text-wrap">{{ __('messages.print-labels.name') }}</span></a>
                        </li>
                    @endcanany

                    @canany(['view_opening_stocks', 'create_opening_stocks', 'update_opening_stocks',
                        'delete_opening_stocks'])
                        <li class="side-menus {{ Request::is('admin/opening-stocks*') ? 'active submenu' : '' }}">
                            <a href="{{ route('opening-stocks.index') }}"><i class="fas fa-lg fa-calculator"></i>
                                <span class="menu-text-wrap">{{ __('messages.opening-stocks.name') }}</span>
                            </a>
                        </li>
                    @endcanany

                </ul>
            </li>
        @endcanany
        @canany(['manage_settings'])
            {{-- <li class="menu-header side-menus">{{ __('messages.others') }}</li> --}}
            <li class="nav-item dropdown side-menus">

                @canany(['manage_items', 'manage_items_groups'])
                @endcanany

                @can('manage_announcements')
                <li class="side-menus {{ Request::is('admin/announcements*') ? 'active' : '' }}">
                    <a href="{{ route('announcements.index') }}"><i class="fas fa-lg fa-bullhorn"></i>
                        <span class="menu-text-wrap">{{ __('messages.announcements') }}</span>
                    </a>
                </li>
            @endcan

            @can('manage_items')
                <li class="side-menus {{ Request::is('admin/products*') ? 'active' : '' }}">
                    <a href="{{ route('products.index') }}"><i class="fas fa-lg fa-sitemap"></i>
                        <span class="menu-text-wrap">{{ __('messages.products.products') }}</span>
                    </a>
                </li>
            @endcan
            {{-- @canany(['manage_items', 'manage_items_groups'])
                <li class="nav-item dropdown side-menus">
                    <a class="nav-link has-dropdown" href="#"><i class="fas fa-lg fa-sitemap"></i>
                        <span>{{ __('messages.products.products') }}</span>
                    </a>
                    <ul class="dropdown-menu side-menus">
                        @can('manage_items')
                            <li class="side-menus {{ Request::is('admin/products*') ? 'active' : '' }}">
                                <a href="{{ route('products.index') }}"><i class="fas fa-lg fa-sitemap"></i>
                                    <span class="menu-text-wrap">{{ __('messages.products.products') }}</span>
                                </a>
                            </li>
                        @endcan
                        @can('manage_items_groups')
                            <li class="side-menus {{ Request::is('admin/product-groups*') ? 'active' : '' }}">
                                <a href="{{ route('product-groups.index') }}"><i class="fas fa-lg fa-object-group"></i>
                                    <span class="menu-text-wrap">{{ __('messages.product_groups') }}</span></a>
                            </li>
                        @endcan

                    </ul>
                </li>
            @endcan --}}
        @endcanany
        {{-- @canany(['manage_customers', 'manage_customer_groups'])
            <li class="menu-header side-menus">{{ __('messages.customers') }}</li>
            <li class="nav-item dropdown side-menus">
                <a class="nav-link has-dropdown" href="#"><i class="fas fa-lg fa-street-view"></i>
                    <span>{{ __('messages.customers') }}</span></a>
                <ul class="dropdown-menu side-menus">
                    @can('manage_customer_groups')
                        <li class="side-menus {{ Request::is('admin/customer-groups*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('customer-groups.index') }}">
                                <i class="fas fa-lg fa-people-arrows"></i>
                                <span class="menu-text-wrap">{{ __('messages.customer_groups') }}</span></a>
                        </li>
                    @endcan

                </ul>
            </li>

        @endcanany --}}

        {{-- task management here --}}
        @canany(['view_task_status', 'create_task_status', 'update_task_status', 'delete_task_status',
            'view_task_assign', 'create_task_assign', 'update_task_assign', 'delete_task_assign'])
            <li class="nav-item dropdown side-menus">
                <a class="nav-link has-dropdown" href="#"><i class="fas fa-lg fa-tasks"></i>
                    <span>{{ __('messages.tasks') }}</span></a>
                <ul class="dropdown-menu side-menus">
                    <li class="side-menus {{ Request::is('admin/task-assign*') ? 'active submenu' : '' }}">
                        <a href="{{ route('task-assign.index') }}"><i class="fas fa-lg fa-user-check"></i>
                            <span class="menu-text-wrap">{{ __('messages.task-assign.name') }}</span>
                        </a>
                    </li>

                    <li class="side-menus {{ Request::is('admin/task-status*') ? 'active submenu' : '' }}">
                        <a href="{{ route('task-status.index') }}"><i class="fas fa-lg fa-tasks"></i>
                            <span class="menu-text-wrap">{{ __('messages.common.task_status') }}</span>
                        </a>
                    </li>

                </ul>
            </li>
        @endcanany
        @canany(['view_projects', 'create_projects', 'update_projects', 'delete_projects'])
            <li class="side-menus {{ Request::is('admin/projects*') ? 'active' : '' }}">
                <a href="{{ route('projects.index') }}">
                    <i class="fas fa-lg fa-layer-group"></i>
                    <span class="menu-text-wrap">{{ __('messages.projects') }}</span>
                </a>
            </li>
        @endcanany
        @canany(['manage_tasks', 'manage_tickets', 'manage_ticket_priority', 'manage_ticket_statuses',
            'manage_predefined_replies'])
            {{-- <li class="menu-header side-menus">{{ __('messages.projects') }} --}}




            <li class="nav-item dropdown side-menus">
                {{-- <a class="nav-link has-dropdown" href="#"><i class="fas fa-lg fa-ticket-alt"></i>
                    <span>{{ __('messages.tickets') }}</span></a> --}}
                <ul class="dropdown-menu side-menus">
                    @can('manage_ticket_priority')
                        <li class="side-menus {{ Request::is('admin/ticket-priorities*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('ticketPriorities.index') }}">
                                <i class="fas fa-lg fa-sticky-note"></i>
                                <span class="menu-text-wrap">{{ __('messages.ticket_priorities') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('manage_ticket_statuses')
                        <li class="side-menus {{ Request::is('admin/ticket-statuses*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('ticket.status.index') }}">
                                <i class="fas fa-lg fa-info-circle"></i><span
                                    class="menu-text-wrap">{{ __('messages.ticket_status.ticket_status') }}</span></a>
                        </li>
                    @endcan
                    @can('manage_predefined_replies')
                        <li class="side-menus {{ Request::is('admin/predefined-replies*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('predefinedReplies.index') }}">
                                <i class="fas fa-lg fa-reply"></i><span
                                    class="menu-text-wrap">{{ __('messages.predefined_replies') }}</span></a>
                        </li>
                    @endcan
                    @can('manage_tickets')
                        <li class="side-menus {{ Request::is('admin/tickets*') ? 'active' : '' }}">
                            <a href="{{ route('ticket.index') }}"><i class="fas fa-lg fa-ticket-alt"></i>
                                <span class="menu-text-wrap">{{ __('messages.tickets') }}</span>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany

        @canany(['view_departments', 'create_departments', 'view_designations', 'create_designations', 'view_employees',
            'create_employees', 'view_transfer', 'update_transfer', 'create_transfer', 'delete_transfer'])
            <li class="nav-item dropdown side-menus">
                <a class="nav-link has-dropdown" href="#"><i class="fas fa-lg fa-money-bill"></i>
                    <span class="menu-text-wrap">{{ __('messages.common.hr') }}</span>
                </a>
                <ul class="dropdown-menu side-menus">

                    @canany(['view_departments', 'create_departments', 'delete_departments'])
                        <li class="side-menus {{ Request::is('admin/departments*') ? 'active   submenu' : '' }}">
                            <a href="{{ route('departments.index') }}"><i class="fas fa-lg fa-columns"></i>
                                <span class="menu-text-wrap">{{ __('messages.department.departments') }}</span>
                            </a>
                        </li>
                    @endcanany
                    @canany(['view_sub_departments', 'create_sub_departments', 'delete_sub_departments'])
                        <li class="side-menus {{ Request::is('admin/sub_departments*') ? 'active   submenu' : '' }}">
                            <a href="{{ route('sub_departments.index') }}"><i class="fas fa-lg fa-th"></i>
                                <span class="menu-text-wrap">{{ __('messages.department.sub_departments') }}</span>
                            </a>
                        </li>
                    @endcan
                    @canany(['view_designations', 'create_designations', 'delete_designations'])
                        <li class="side-menus {{ Request::is('admin/designations*') ? 'active  submenu' : '' }}">
                            <a href="{{ route('designations.index') }}"><i class="fas fa-lg fa-bars"></i>
                                <span class="menu-text-wrap">{{ __('messages.designations.name') }}</span>
                            </a>
                        </li>
                    @endcanany
                    @canany(['view_employees', 'create_employees', 'delete_employees'])
                        <li class="side-menus {{ Request::is('admin/employees*') ? 'active   submenu' : '' }}">
                            <a href=" {{ route('employees.index') }} "><i class="fas fa-lg fa-user-tie"></i>
                                <span class="menu-text-wrap">{{ __('messages.employees.name') }}</span>
                            </a>
                        </li>
                    @endcanany
                    @canany(['view_transfer', 'update_transfer', 'create_transfer', 'delete_transfer'])
                        <li class="side-menus {{ Request::is('admin/transfers*') ? 'active   submenu' : '' }}">
                            <a href=" {{ route('transfers.index') }} "><i class="fas fa-lg fa-exchange-alt"></i>
                                <span class="menu-text-wrap">{{ __('messages.transfers.name') }}</span>
                            </a>
                        </li>
                    @endcanany

                    {{-- <li class="nav-item side-menus {{ Request::is('casuel-employees*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('casual.employees.index') }}">
                            <i class="nav-icon fas fa-users"></i>
                            <span class="menu-text-wrap">Casual Employees</span>
                        </a>
                    </li> --}}

                    {{-- @canany(['view_employees', 'create_employees', 'delete_employees']) --}}
                    <li class="side-menus {{ Request::is('admin/casual-employees*') ? 'active   submenu' : '' }}">
                        <a href=" {{ route('casual.employees.index') }} "><i class="fas fa-lg fa-user-tie"></i>
                            <span class="menu-text-wrap">{{ __('messages.casual_employees.name') }}</span>
                        </a>
                    </li>
                    {{--       @endcanany --}}


                    <li
                        class="side-menus {{ Request::is('admin/casual-employee-timesheets*') ? 'active submenu' : '' }}">
                        <a href="{{ route('casual-employee-timesheets.index') }}">
                            <i class="fas fa-lg fa-clock"></i>
                            <span class="menu-text-wrap">Casual Employee Timesheets</span>
                        </a>
                    </li>


                </ul>
            </li>
        @endcanany
        @canany(['view_holidays', 'create_holidays', 'update_holidays', 'delete_holidays'])
            <li class="side-menus {{ Request::is('admin/holidays*') ? 'active' : '' }}">
                <a href="{{ route('holidays.index') }}"><i class="fas fa-lg fa-mug-hot"></i>
                    <span class="menu-text-wrap">{{ __('messages.holidays.holidays') }}</span>
                </a>
            </li>
        @endcanany

        @canany(['view_appointments', 'create_appointments', 'update_appointments', 'delete_appointments'])
            <li class="side-menus {{ Request::is('admin/appointments*') ? 'active' : '' }}">
                <a href="{{ route('appointments.index') }}"><i class="fas fa-lg fa-calendar"></i>
                    <span class="menu-text-wrap">{{ __('messages.appointments.appointments') }}</span>
                </a>
            </li>
        @endcanany

        @canany(['view_leave_groups', 'create_leave_groups', 'update_leave_groups', 'delete_leave_groups',
            'view_leave_applications', 'create_leave_applications', 'update_leave_applications',
            'delete_leave_applications'])
            <li class="nav-item dropdown side-menus">
                <a class="nav-link has-dropdown" href="#"><i class="fas fa-lg fa-bed"></i>
                    <span>{{ __('messages.leaves.leaves') }}</span></a>
                <ul class="dropdown-menu side-menus">
                    @canany(['view_leave_groups', 'create_leave_groups', 'update_leave_groups', 'delete_leave_groups'])
                        <li class="side-menus {{ Request::is('admin/leaves*') ? 'active submenu' : '' }}">
                            <a href="{{ route('leaves.index') }}"><i class="fas fa-lg fa-bed"></i>
                                <span class="menu-text-wrap">{{ __('messages.leaves.name') }}</span>
                            </a>
                        </li>
                    @endcanany
                    @canany(['view_leave_applications', 'create_leave_applications', 'update_leave_applications',
                        'delete_leave_applications'])
                        <li class="side-menus {{ Request::is('admin/leave-applications*') ? 'active submenu' : '' }}">
                            <a href="{{ route('leave-applications.index') }}"><i class="fas fa-lg fa-file-alt"></i>
                                <span
                                    class="menu-text-wrap">{{ __('messages.leave-applications.leave-applications') }}</span>
                            </a>
                        </li>
                    @endcanany
                    @canany(['view_approval_leaves', 'approve_approval_leaves'])
                        <li class="side-menus {{ Request::is('admin/approval-leaves*') ? 'active submenu' : '' }}">
                            <a href="{{ route('approval-leaves.index') }}">
                                <i class="fas fa-lg fa-check-circle"></i> <!-- Use a relevant icon here -->
                                <span class="menu-text-wrap">{{ __('messages.approval-leaves.menu') }}</span>
                            </a>
                        </li>
                    @endcanany


                </ul>
            </li>
        @endcanany
        @canany(['create_manage_attendances', 'import_manage_attendances', 'export_manage_attendances'])
            <li class="side-menus {{ Request::is('admin/manage-attendances*') ? 'active  submenu' : '' }}">
                <a href="{{ route('manage-attendances.index') }}"><i class="fas fa-lg fa-calendar-check"></i>
                    <span class="menu-text-wrap">{{ __('messages.manage_attendances.name') }}</span>
                </a>
            </li>
        @endcanany
        @canany(['view_salary_sheet', 'export_salary_sheet', 'create_salary_sheet', 'view_employee_salaries',
            'export_employee_salaries', 'view_allowances', 'create_allowances', 'update_allowances', 'view_salary_advances',
            'create_salary_advances', 'update_salary_advances', 'view_loan', 'create_loan', 'update_loan', 'delete_loan',
            'view_insurances', 'create_insurances', 'view_bonuses', 'update_bonuses', 'create_bonuses', 'view_deductions',
            'create_deductions', 'view_statement_employee', 'export_statement_employee'])
            <li class="nav-item dropdown side-menus">
                <a class="nav-link has-dropdown" href="#"><i class="fas fa-lg fa-wallet"></i>
                    <span>{{ __('Payroll') }}</span></a>
                <ul class="dropdown-menu side-menus">
                    @canany(['view_salary_sheet', 'export_salary_sheet', 'create_salary_sheet'])
                        <li class="side-menus {{ Request::is('admin/salary_generates*') ? 'active submenu' : '' }}">
                            <a href="{{ route('salary_generates.index') }}"><i class="fas fa-lg fa-pen"></i>
                                <span class="menu-text-wrap">{{ __('messages.salary_generates.name') }}</span>
                            </a>
                        </li>
                    @endcanany

                    @canany(['view_employee_salaries', 'export_employee_salaries'])
                        <li class="side-menus {{ Request::is('admin/employee-salaries*') ? 'active submenu' : '' }}">
                            <a href="{{ route('employee-salaries.index') }}"><i class="fas fa-lg fa-clipboard-list"></i>
                                <span class="menu-text-wrap">{{ __('messages.employee_salaries.payslip') }}</span>
                            </a>
                        </li>
                    @endcanany

                    @canany(['view_allowances', 'create_allowances', 'update_allowances', 'delete_allowances'])
                        <li class="side-menus {{ Request::is('admin/allowances*') ? 'active submenu' : '' }}">
                            <a href="{{ route('allowances.index') }}"><i class="fas fa-lg fa-wallet"></i>
                                <span class="menu-text-wrap">{{ __('messages.allowances.allowances') }}</span>
                            </a>
                        </li>
                    @endcanany

                    @canany(['view_salary_advances', 'create_salary_advances', 'update_salary_advances',
                        'delete_salary_advances'])
                        <li class="side-menus {{ Request::is('admin/salary_advances*') ? 'active submenu' : '' }}">
                            <a href="{{ route('salary_advances.index') }}"><i class="fas fa-lg fa-wallet"></i>
                                <span class="menu-text-wrap">{{ __('messages.salary_advances.name') }}</span>
                            </a>
                        </li>
                    @endcanany

                    @canany(['view_loan', 'create_loan', 'update_loan', 'delete_loan'])
                        <li class="side-menus {{ Request::is('admin/loans*') ? 'active submenu' : '' }}">
                            <a href="{{ route('loans.index') }}"><i class="fas fa-lg fa-hand-holding-usd"></i>
                                <span class="menu-text-wrap">{{ __('messages.loans.name') }}</span>
                            </a>
                        </li>
                    @endcanany

                    @canany(['view_insurances', 'create_insurances', 'update_insurances', 'delete_insurances'])
                        <li class="side-menus {{ Request::is('admin/insurances*') ? 'active submenu' : '' }}">
                            <a href="{{ route('insurances.index') }}"><i class="fa fa-shield-alt" aria-hidden="true"></i>
                                <span class="menu-text-wrap">{{ __('messages.insurances.insurances') }}</span>
                            </a>
                        </li>
                    @endcanany

                    @canany(['view_bonuses', 'update_bonuses', 'create_bonuses'])
                        <li class="side-menus {{ Request::is('admin/bonuses*') ? 'active submenu' : '' }}">
                            <a href="{{ route('bonuses.index') }}"><i class="fas fa-lg fa-gift"></i>
                                <span class="menu-text-wrap">{{ __('messages.bonuses.bonuses') }}</span>
                            </a>
                        </li>
                    @endcanany
                    @canany(['view_deductions', 'create_deductions', 'update_deductions', 'delete_deductions'])
                        <li class="side-menus {{ Request::is('admin/deductions*') ? 'active submenu' : '' }}">
                            <a href="{{ route('deductions.index') }}"><i class="fas fa-lg fa-minus-circle"></i>
                                <span class="menu-text-wrap">{{ __('messages.deductions.deductions') }}</span>
                            </a>
                        </li>
                    @endcanany
                    @canany(['view_statement_employee', 'export_statement_employee'])
                        <li class="side-menus {{ Request::is('admin/employee-statements*') ? 'active submenu' : '' }}">
                            <a href="{{ route('employee-statements.index') }}"><i class="fas fa-lg fa-file-alt"></i>
                                <span class="menu-text-wrap">{{ __('messages.employee-statements.name') }}</span>
                            </a>
                        </li>
                    @endcanany


                </ul>
            </li>
        @endcanany
        @canany(['view_increments', 'create_increments', 'update_increments', 'delete_increments'])
            <li class="side-menus {{ Request::is('admin/increments*') ? 'active' : '' }}">
                <a href="{{ route('increments.index') }}"><i class="fas fa-lg fa-plus-circle"></i>
                    <span class="menu-text-wrap">{{ __('messages.increments.increments') }}</span>
                </a>
            </li>
        @endcanany
        @canany(['view_retirements', 'create_retirements', 'update_retirements', 'delete_retirements'])
            <li class="side-menus {{ Request::is('admin/retirements*') ? 'active' : '' }}">
                <a href="{{ route('retirements.index') }}"><i class="fa fa-umbrella-beach" aria-hidden="true"></i>
                    <span class="menu-text-wrap">{{ __('messages.retirements.retirements') }}</span>
                </a>
            </li>
        @endcanany
        @canany(['view_terminations', 'create_terminations', 'update_terminations', 'delete_terminations'])
            <li class="side-menus {{ Request::is('admin/terminations*') ? 'active' : '' }}">
                <a href="{{ route('terminations.index') }}">
                    <i class="fa fa-user-times" aria-hidden="true"></i>
                    <span class="menu-text-wrap">{{ __('messages.terminations.terminations') }}</span>
                </a>
            </li>
        @endcanany

        @canany(['view_revokes', 'create_revokes', 'update_revokes', 'delete_revokes'])
            <li class="side-menus {{ Request::is('admin/revokes*') ? 'active' : '' }}">
                <a href="{{ route('revokes.index') }}">
                    <i class="fa fa-undo" aria-hidden="true"></i>
                    <span class="menu-text-wrap">{{ __('messages.revokes.revokes') }}</span>
                </a>
            </li>
        @endcanany



        @canany(['manage_terms'])
            <li class="side-menus {{ Request::is('admin/terms*') ? 'active' : '' }}">
                <a href="{{ route('terms.index') }}">
                    <i class="fa fa-file-contract" aria-hidden="true"></i>
                    <span class="menu-text-wrap">{{ __('messages.terms.name') }}</span>
                </a>
            </li>
        @endcanany




        {{-- @canany(['view_suppliers', 'create_suppliers', 'view_supplier_groups', 'create_supplier_groups'])
            <li class="nav-item dropdown side-menus">
                <a class="nav-link has-dropdown" href="#"><i class="fas fa-lg fas fa-lg fa-briefcase"></i>
                    <span class="menu-text-wrap">{{ __('messages.suppliers.suppliers') }}</span>
                </a>
                <ul class="dropdown-menu side-menus">
                    @canany(['view_supplier_groups', 'create_supplier_groups'])
                        <li class="side-menus {{ Request::is('admin/supplier-groups*') ? 'active' : '' }}">
                            <a href="{{ route('supplier-groups.index') }}"><i class="fas fa-lg fa-lg fa-th "></i>
                                <span class="menu-text-wrap">{{ __('messages.supplier_groups.supplier_groups') }}</span>
                            </a>
                        </li>
                    @endcanany

                </ul>
            </li>
        @endcanany --}}
        @canany(['view_suppliers', 'create_suppliers'])
            <li class="side-menus {{ Request::is('admin/suppliers*') ? 'active' : '' }}">
                <a href="{{ route('suppliers.index') }}"><i class="fas fa-lg fa-briefcase"></i>
                    <span class="menu-text-wrap">{{ __('messages.suppliers.suppliers') }}</span>
                </a>
            </li>
        @endcanany

        @canany(['manage_articles', 'manage_article_groups'])
            <li class="nav-item dropdown side-menus">
                <a class="nav-link has-dropdown" href="#"><i class="fab fa-lg fa-autoprefixer"></i>
                    <span>{{ __('messages.articles') }}</span></a>
                <ul class="dropdown-menu side-menus">
                    @can('manage_article_groups')
                        <li class="side-menus {{ Request::is('admin/article-groups*') ? 'active' : '' }}">
                            <a href="{{ route('article-groups.index') }}"><i class="fas fa-lg fa-edit"></i>
                                <span class="menu-text-wrap">{{ __('messages.article_group.article_groups') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('manage_articles')
                        <li class="side-menus {{ Request::is('admin/articles*') ? 'active' : '' }}">
                            <a href="{{ route('articles.index') }}"><i class="fab fa-lg fa-autoprefixer"></i>
                                <span class="menu-text-wrap">{{ __('messages.articles') }}</span>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany
        @can('manage_tags')
            <li class="side-menus {{ Request::is('admin/tags*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('tags.index') }}">
                    <i class="fas fa-tags"></i><span class="menu-text-wrap">{{ __('messages.tags') }}</span>
                </a>
            </li>
        @endcan
        @canany(['manage_lead_status', 'manage_lead_sources', 'manage_leads', 'view_leads'])
            <li class="menu-header side-menus">{{ __('messages.leads') }}</li>
            <li class="nav-item dropdown side-menus">
                <a class="nav-link has-dropdown" href="#"><i class="fas fa-lg fa-tty"></i>
                    <span>{{ __('messages.leads') }}</span></a>
                <ul class="dropdown-menu side-menus">
                    @can('manage_lead_status')
                        <li class="side-menus {{ Request::is('admin/lead-status*') ? 'active' : '' }}">
                            <a href="{{ route('lead.status.index') }}"><i class="fas fa-lg fa-blender-phone"></i>
                                <span class="menu-text-wrap">{{ __('messages.lead_status.lead_status') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('manage_lead_sources')
                        <li class="side-menus {{ Request::is('admin/lead-sources*') ? 'active' : '' }}">
                            <a href="{{ route('lead.source.index') }}"><i class="fas fa-lg fa-globe"></i>
                                <span class="menu-text-wrap">{{ __('messages.lead_sources') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('view_leads')
                        <li class="side-menus {{ Request::is('admin/leads*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('leads.index') }}">
                                <i class="fas fa-lg fa-tty"></i><span
                                    class="menu-text-wrap">{{ __('messages.leads') }}</span>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany




        {{-- @canany(['view_departments', 'create_departments', 'delete_departments', 'view_sub_departments', 'create_sub_departments', 'delete_sub_departments'])
            <li class="menu-header side-menus">{{ __('messages.support') }}</li>
            <li class="nav-item dropdown side-menus">
                <a class="nav-link has-dropdown" href="#"><i class="fas fa-lg fa-columns"></i>
                    <span class="menu-text-wrap">{{ __('messages.department.departments') }}</span>
                </a>
                <ul class="dropdown-menu side-menus">
                    @canany(['view_departments', 'create_departments', 'delete_departments'])
                        <li class="side-menus {{ Request::is('admin/departments*') ? 'active' : '' }}">
                            <a href="{{ route('departments.index') }}"><i class="fas fa-lg fa-columns"></i>
                                <span class="menu-text-wrap">{{ __('messages.department.departments') }}</span>
                            </a>
                        </li>
                    @endcanany
                    @canany(['view_sub_departments', 'create_sub_departments', 'delete_sub_departments'])
                        <li class="side-menus {{ Request::is('admin/sub_departments*') ? 'active' : '' }}">
                            <a href="{{ route('sub_departments.index') }}"><i class="fas fa-lg fa-th"></i>
                                <span class="menu-text-wrap">{{ __('messages.department.sub_departments') }}</span>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany --}}






        @canany(['create_attendances', 'update_attendances', 'delete_attendances', 'import_attendances'])
            <li class="side-menus {{ Request::is('admin/attendances*') ? 'active' : '' }}">
                <a href=" {{ route('attendances.index') }} "><i class="fas fa-check"></i>
                    <span class="menu-text-wrap">{{ __('messages.attendances.name') }}</span>
                </a>
            </li>
        @endcanany
        {{-- {{ route('shift.index') }} --}}
        @canany(['view_shifts', 'create_shifts', 'update_shifts', 'delete_shifts'])
            <li class="side-menus {{ Request::is('admin/shifts*') ? 'active' : '' }}">
                <a href=" {{ route('shifts.index') }} "><i class="fas fa-lg fa-clock"></i>
                    <span class="menu-text-wrap">{{ __('messages.shifts.shifts') }}</span>
                </a>
            </li>
        @endcanany
        {{-- payroll starts here --}}
        {{-- @canany(['create_generate_salaries', 'approve_generate_salaries', 'view_generate_salaries', 'export_generate_salaries', 'view_employee_salaries', 'export_employee_salaries', 'create_manage_attendances', 'import_manage_attendances', 'export_manage_attendances', 'view_employees', 'create_employees', 'delete_employees', 'view_designations', 'create_designations', 'delete_designations', 'view_leave_groups', 'create_leave_groups', 'update_leave_groups', 'delete_leave_groups', 'view_leave_applications', 'create_leave_applications', 'update_leave_applications', 'delete_leave_applications']) --}}

        {{-- @endcanany --}}
        {{-- payroll ends here --}}




        @canany(['view_overtimes', 'create_overtimes', 'update_overtimes', 'delete_overtimes'])
            <li class="side-menus {{ Request::is('admin/overtimes*') ? 'active' : '' }}">
                <a href="{{ route('overtimes.index') }}"><i class="fas fa-lg fa-clock"></i>
                    <span class="menu-text-wrap">{{ __('messages.overtimes.overtimes') }}</span>
                </a>
            </li>
        @endcanany










        @canany(['view_rentals', 'create_rentals', 'update_rentals', 'delete_rentals'])
            <li class="side-menus {{ Request::is('admin/rentals*') ? 'active' : '' }}">
                <a href="{{ route('rentals.index') }}"><i class="fa fa-building" aria-hidden="true"></i>
                    <span class="menu-text-wrap">{{ __('messages.rentals.rentals') }}</span>
                </a>
            </li>
        @endcanany

        @canany(['view_awards', 'create_awards', 'update_awards', 'delete_awards'])
            <li class="side-menus {{ Request::is('admin/awards*') ? 'active' : '' }}">
                <a href="{{ route('awards.index') }}"><i class="fa fa-trophy" aria-hidden="true"></i>
                    <span class="menu-text-wrap">{{ __('messages.awards.awards') }}</span>
                </a>
            </li>
        @endcanany

        @canany(['view_commissions', 'create_commissions', 'update_commissions', 'delete_commissions'])
            <li class="side-menus {{ Request::is('admin/commissions*') ? 'active' : '' }}">
                <a href="{{ route('commissions.index') }}"><i class="fa fa-coins" aria-hidden="true"></i>
                    <span class="menu-text-wrap">{{ __('messages.commissions.commissions') }}</span>
                </a>
            </li>
        @endcanany



        @canany([
            'view_vat_reports',
            'pay_vat_reports',
            'update_vat_reports',
            'export_vat_reports',
            'view_profit_and_loss',
            'export_profit_and_loss',
            'view_expense_sub_categories',
            'create_expense_sub_categories',
            'update_expense_sub_categories',
            'delete_expense_sub_categories',
            'view_expense_categories',
            'create_expense_categories',
            'update_expense_categories',
            'delete_expense_categories',
            'view_expenses',
            'create_expenses',
            'update_expenses',
            'delete_expenses',
            'export_expenses',
            'manage_payment_mode',
            'view_accounts',
            'create_accounts',
            'update_accounts',
            'delete_accounts',
            'view_cash_transfers',
            'create_cash_transfers',
            'update_cash_transfers',
            'delete_cash_transfers',
            'export_cash_transfers',
            'view_journal_vouchers',
            'create_journal_vouchers',
            'update_journal_vouchers',
            'delete_journal_vouchers',
            'export_journal_vouchers',
            'view_account_statements',
            'export_account_statements',
            'view_print_checks',
            'create_print_checks',
            'update_print_checks',
            'delete_print_checks',
            'view_statement_supplier',
            ])
            {{-- <li class="menu-header side-menus">{{ __('messages.expenses') }}</li> --}}

            <li class="nav-item dropdown side-menus">
                <a class="nav-link has-dropdown" href="#"><i class="fab fa-lg fa-erlang"></i>
                    <span>{{ __('messages.expenses') }}</span></a>
                <ul class="dropdown-menu side-menus">


                    @can('manage_payment_mode')
                        <li class="side-menus {{ Request::is('admin/payment-modes*') ? 'active  submenu' : '' }}">
                            <a href="{{ route('payment-modes.index') }}"><i class="fab fa-lg fa-product-hunt"></i>
                                <span class="menu-text-wrap">{{ __('messages.payment_modes') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('manage_tax_rates')
                        <li class="side-menus {{ Request::is('admin/tax-rates*') ? 'active  submenu' : '' }}">
                            <a href="{{ route('tax-rates.index') }}"><i class="fas fa-lg fa-percent"></i>
                                <span class="menu-text-wrap">{{ __('messages.tax_rates') }}</span>
                            </a>
                        </li>
                    @endcan
                    @canany(['view_expense_categories', 'create_expense_categories', 'update_expense_categories',
                        'delete_expense_categories'])
                        <li class="side-menus {{ Request::is('admin/expense-categories*') ? 'active   submenu' : '' }}">
                            <a href="{{ route('expense-categories.index') }}"><i class="fas fa-lg fa-list-ol"></i>
                                <span class="menu-text-wrap">{{ __('messages.expense_categories') }}</span>
                            </a>
                        </li>
                    @endcanany
                    @canany(['view_expense_sub_categories', 'create_expense_sub_categories',
                        'update_expense_sub_categories', 'delete_expense_sub_categories'])
                        <li class="side-menus {{ Request::is('admin/expense_sub_categories*') ? 'active   submenu' : '' }}">
                            <a href="{{ route('expense_sub_categories.index') }}"><i class="fas fa-lg fa-indent"></i>
                                <span class="menu-text-wrap">{{ __('messages.expense_sub_categories.name') }}</span>
                            </a>
                        </li>
                    @endcanany
                    @canany(['view_expenses', 'create_expenses', 'update_expenses', 'delete_expenses', 'export_expenses'])
                        <li class="side-menus {{ Request::is('admin/expenses*') ? 'active   submenu' : '' }}">
                            <a href="{{ route('expenses.index') }}"><i class="fab fa-lg fa-erlang"></i>
                                <span class="menu-text-wrap">{{ __('messages.payment_voucher.name') }}</span>
                            </a>
                        </li>
                    @endcanany
                    @canany(['view_vat_reports', 'pay_vat_reports', 'update_vat_reports', 'export_vat_reports'])
                        <li class="side-menus {{ Request::is('admin/vat-reports*') ? 'active   submenu' : '' }}">
                            <a href="{{ route('vat-reports.index') }}"><i class="fas fa-lg fa-chart-bar"></i>
                                <span class="menu-text-wrap">{{ __('messages.vat-reports.name') }}</span>
                            </a>
                        </li>
                    @endcanany
                    @canany(['view_profit_and_loss', 'export_profit_and_loss'])
                        <li class="side-menus {{ Request::is('admin/profit-loss*') ? 'active   submenu' : '' }}">
                            <a href="{{ route('profit-loss.index') }}"><i class="fas fa-lg fa-chart-line"></i>
                                <span class="menu-text-wrap">{{ __('messages.profit-loss.name') }}</span>
                            </a>
                        </li>
                    @endcanany
                    @canany(['view_accounts', 'create_accounts', 'update_accounts', 'delete_accounts', 'export_accounts'])
                        <li class="side-menus {{ Request::is('admin/accounts*') ? 'active   submenu' : '' }}">
                            <a href="{{ route('accounts.index') }}"><i class="fas fa-lg fa-money-bill"></i>
                                <span class="menu-text-wrap">{{ __('messages.accounts.accounts') }}</span>
                            </a>
                        </li>
                    @endcanany
                    {{-- @canany(['view_cash_transfers', 'create_cash_transfers', 'update_cash_transfers', 'delete_cash_transfers', 'export_cash_transfers'])
                        <li class="side-menus {{ Request::is('admin/cash-transfers*') ? 'active   submenu' : '' }}">
                            <a href="{{ route('cash-transfers.index') }}"><i class="fas fa-lg fa-exchange-alt"></i>
                                <span class="menu-text-wrap">{{ __('messages.cash-transfers.name') }}</span>
                            </a>
                        </li>
                    @endcanany

                    @canany(['view_journal_vouchers', 'create_journal_vouchers', 'update_journal_vouchers', 'delete_journal_vouchers', 'export_journal_vouchers'])
                        <li class="side-menus {{ Request::is('admin/journal-vouchers*') ? 'active   submenu' : '' }}">
                            <a href="{{ route('journal-vouchers.index') }}"><i class="fas fa-lg fa-book"></i>
                                <span class="menu-text-wrap">{{ __('messages.journal-vouchers.name') }}</span>
                            </a>
                        </li>
                    @endcanany --}}
                    @canany(['view_account_statements', 'export_account_statements'])
                        <li class="side-menus {{ Request::is('admin/account-statements*') ? 'active   submenu' : '' }}">
                            <a href="{{ route('account-statements.index') }}"><i class="fas fa-lg fa-bars"></i>
                                <span class="menu-text-wrap">{{ __('messages.account-statements.menu') }}</span>
                            </a>
                        </li>
                    @endcanany

                    @can('view_statement_supplier')
                        <li class="side-menus {{ Request::is('admin/supplier-statement*') ? 'active  submenu' : '' }}">
                            <a href="{{ route('supplier-statement.index') }}"><i class="fas fa-lg fa-receipt"></i>
                                <span class="menu-text-wrap">{{ __('messages.supplier_statements.name') }}</span>
                            </a>
                        </li>
                    @endcan

                    @canany(['view_print_checks', 'create_print_checks', 'update_print_checks', 'delete_print_checks'])
                        <li class="side-menus {{ Request::is('admin/print-checks*') ? 'active   submenu' : '' }}">
                            <a href="{{ route('print-checks.index') }}"><i class="fas fa-lg fa-money-check"></i>

                                <span class="menu-text-wrap">{{ __('messages.print-checks.name') }}</span>
                            </a>
                        </li>
                    @endcanany

                </ul>
            </li>
        @endcanany

        @canany(['view_master_accounts', 'create_master_accounts', 'update_master_accounts', 'delete_master_accounts'])
            <li class="nav-item dropdown side-menus">
                <a class="nav-link has-dropdown" href="#">
                    <i class="fa fa-book" aria-hidden="true"></i>
                    <span>{{ __('messages.master_accounts.account') }}</span>
                </a>
                <ul class="dropdown-menu side-menus">
                    {{-- Master Accounts --}}
                    <li class="side-menus {{ Request::is('admin/master-accounts*') ? 'active submenu' : '' }}">
                        <a href="{{ route('master-accounts.index') }}">
                            <i class="fa fa-book" aria-hidden="true"></i>
                            <span class="menu-text-wrap">{{ __('messages.master_accounts.accounts') }}</span>
                        </a>
                    </li>
                    {{-- Trading Accounts --}}
                    <li class="side-menus {{ Request::is('admin/trading-accounts*') ? 'active submenu' : '' }}">
                        <a href="{{ route('trading-accounts.index') }}">
                            <i class="fas fa-lg fa-coins" aria-hidden="true"></i>
                            <span class="menu-text-wrap">{{ __('messages.trading_accounts.trading_account') }}</span>
                        </a>
                    </li>

                    {{-- Profit & Loss --}}
                    <li class="side-menus {{ Request::is('admin/profit-and-loss.index*') ? 'active submenu' : '' }}">
                        <a href="{{ route('profit-and-loss.index') }}">
                            <i class="fa fa-chart-line" aria-hidden="true"></i>
                            <span class="menu-text-wrap">{{ __('messages.profit_loss.profit_loss') }}</span>
                        </a>
                    </li>

                    {{-- Balance Sheet (under same permission) --}}
                    <li class="side-menus {{ Request::is('admin/balance-sheets*') ? 'active submenu' : '' }}">
                        <a href="{{ route('balance-sheets.index') }}">
                            <i class="fa fa-balance-scale" aria-hidden="true"></i>
                            <span class="menu-text-wrap">{{ __('messages.balance_sheets.balance_sheet') }}</span>
                        </a>
                    </li>
                </ul>
            </li>
        @endcanany

        @canany(['view_assets', 'create_assets', 'update_assets', 'delete_assets', 'view_asset_categories',
            'create_asset_categories', 'update_asset_categories', 'delete_asset_categories', 'view_vehicle_rental',
            'create_vehicle_rental', 'update_vehicle_rental', 'delete_vehicle_rental'])
            <li class="nav-item dropdown side-menus">
                <a class="nav-link has-dropdown" href="#"><i class="fas fa-lg fa-industry"></i>
                    <span>{{ __('messages.assets.menu') }}</span>
                </a>
                <ul class="dropdown-menu side-menus">
                    @canany(['view_asset_categories', 'create_asset_categories', 'update_asset_categories',
                        'delete_asset_categories'])
                        <li class="side-menus {{ Request::is('admin/asset_category*') ? 'active   submenu' : '' }}">
                            <a href="{{ route('asset.category.index') }}"><i class="fas fa-lg fa-bars"></i>
                                <span class="menu-text-wrap">{{ __('messages.assets.menu_category') }}</span></a>
                        </li>
                    @endcanany
                    @canany(['view_assets', 'create_assets', 'update_assets', 'delete_assets'])
                        <li class="side-menus {{ Request::is('admin/assets*') ? 'active   submenu' : '' }}">
                            <a href="{{ route('assets.index') }}"><i class="fas fa-lg fa-industry"></i>
                                <span class="menu-text-wrap">{{ __('messages.assets.menu_asset') }}</span>
                            </a>
                        </li>
                    @endcanany

                    @canany(['view_vehicle_rental', 'create_vehicle_rental', 'update_vehicle_rental',
                        'delete_vehicle_rental'])
                        <li class="side-menus {{ Request::is('admin/vehicle-rentals*') ? 'active submenu' : '' }}">
                            <a href="{{ route('vehicle-rentals.index') }}"><i class="fas fa-lg fa-car"></i>
                                <span class="menu-text-wrap">{{ __('messages.vehicle-rentals.name') }}</span>
                            </a>
                        </li>
                    @endcanany



                </ul>
            </li>
        @endcanany

        {{-- <li class="side-menus {{ Request::is('admin/notices*') ? 'active' : '' }}">
            <a href="{{ route('notices.index') }}"><i class="fas fa-lg fa-bell"></i>
                <span class="menu-text-wrap">{{ __('messages.notices.notices') }}</span>
            </a>
        </li> --}}

        <li class="nav-item dropdown side-menus">
            <a class="nav-link has-dropdown" href="#"><i class="fas fa-lg fa-chart-line"></i>
                <span>{{ __('messages.sales_reports.menu') }}</span>
            </a>
            <ul class="dropdown-menu side-menus">
                <li class="side-menus {{ Request::is('sales-reports*') ? 'active submenu' : '' }}">
                    <a href="{{ route('sales-reports.index') }}"><i class="fas fa-lg fa-file-invoice-dollar"></i>
                        <span class="menu-text-wrap">{{ __('messages.sales_reports.menu_sales_report') }}</span>
                    </a>
                </li>

                <li class="side-menus {{ Request::is('sales-tax-reports*') ? 'active submenu' : '' }}">
                    <a href="{{ route('sales-tax-reports.index') }}"><i
                            class="fas fa-lg fa-file-invoice-dollar"></i>
                        <span class="menu-text-wrap">{{ __('messages.sales_tax_report.menu') }}</span>
                    </a>
                </li>

                <li class="side-menus {{ Request::is('sales-item-reports*') ? 'active submenu' : '' }}">
                    <a href="{{ route('sales-item-reports.index') }}">
                        <i class="fas fa-lg fa-shopping-cart"></i>
                        <span class="menu-text-wrap">{{ __('messages.sales_item_reports.name') }}</span>
                    </a>
                </li>

                <li class="side-menus {{ Request::is('credit-note-reports*') ? 'active submenu' : '' }}">
                    <a href="{{ route('credit-note-reports.index') }}">
                        <i class="fas fa-lg fa-file-invoice"></i>
                        <span class="menu-text-wrap">{{ __('messages.credit_note_reports.menu') }}</span>
                    </a>
                </li>
            </ul>
        </li>


        @canany(['manage_contracts', 'manage_contracts_types'])
            <li class="nav-item dropdown side-menus">
                <a class="nav-link has-dropdown" href="#"><i class="fas fa-lg fa-file-signature"></i>
                    <span>{{ __('messages.contracts') }}</span>
                </a>
                <ul class="dropdown-menu side-menus">
                    @can('manage_contracts')
                        <li class="side-menus {{ Request::is('admin/contracts*') ? 'active' : '' }}">
                            <a href="{{ route('contracts.index') }}"><i class="fas fa-lg fa-file-signature"></i>
                                <span class="menu-text-wrap">{{ __('messages.contracts') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('manage_contracts_types')
                        <li class="side-menus {{ Request::is('admin/contract-types*') ? 'active' : '' }}">
                            <a href="{{ route('contract-types.index') }}"><i class="fas fa-lg fa-file-contract"></i>
                                <span class="menu-text-wrap">{{ __('messages.contract_types') }}</span>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany
        @can('manage_goals')
            <li class="side-menus {{ Request::is('admin/goals*') ? 'active' : '' }}">
                <a href="{{ route('goals.index') }}"><i class="fas fa-lg fa-bullseye"></i>
                    <span class="menu-text-wrap">{{ __('messages.goals') }}</span>
                </a>
            </li>
        @endcan

        {{-- <li class="menu-header side-menus">{{ __('messages.common.admin') }}</li> --}}
        @canany(['view_currencies', 'create_currencies', 'delete_currencies'])
            <li class="side-menus {{ Request::is('admin/currencies*') ? 'active' : '' }}">
                <a href="{{ route('currencies.index') }}"><i class="fas fa-lg fa-coins"></i>
                    <!-- Updated icon -->
                    <span class="menu-text-wrap">{{ __('messages.currencies.currencies') }}</span>
                    <!-- Updated translation key -->
                </a>
            </li>
        @endcanany
        @can('manage_countries')
            <li class="side-menus {{ Request::is('admin/countries*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('countries.index') }}">
                    <i class="fas fa-lg fa-globe-asia"></i>
                    <span class="menu-text-wrap">{{ __('messages.countries') }}</span>
                </a>
            </li>
        @endcan
        @canany(['view_states', 'create_states', 'update_states', 'delete_states'])
            <li class="side-menus {{ Request::is('admin/states*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('states.index') }}">
                    <i class="fas fa-lg fa-map"></i>
                    <span class="menu-text-wrap">{{ __('messages.states.states') }}</span>
                </a>
            </li>
        @endcanany
        @canany(['view_locations', 'create_locations', 'update_locations', 'delete_locations'])
            <li class="side-menus {{ Request::is('admin/locations*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('locations.index') }}">
                    <i class="fas fa-lg fa-map-marker-alt"></i>
                    <span class="menu-text-wrap">{{ __('messages.locations.locations') }}</span>
                </a>
            </li>
        @endcanany
        @canany(['view_job_sources', 'create_job_sources', 'update_job_sources', 'delete_job_sources'])
            <li class="side-menus {{ Request::is('admin/job-sources*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('job-sources.index') }}">
                    <i class="fas fa-lg fa-briefcase"></i>
                    <span class="menu-text-wrap">{{ __('messages.job_sources.states') }}</span>
                </a>
            </li>
        @endcanany
        @can('manage_settings')
            <li class="nav-item side-menus {{ Request::is('admin/settings*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('settings.show', ['group' => 'company_information']) }}">
                    <i class="nav-icon fa-lg fas fa-industry"></i>
                    <span class="menu-text-wrap">{{ __('messages.settings') }}</span>
                </a>
            </li>
        @endcan
        @canany(['view_branches', 'create_branches'])
            <li class="side-menus {{ Request::is('admin/branches*') ? 'active' : '' }}">
                <a href="{{ route('branches.index') }}"><i class="fas fa-lg fa-building"></i>
                    <!-- Changed icon to fa-building -->
                    <span class="menu-text-wrap">{{ __('messages.branches.branches') }}</span>
                    <!-- Updated translation key -->
                </a>
            </li>
        @endcanany
        @canany(['view_banks', 'create_banks', 'update_banks', 'delete_banks'])
            <li class="side-menus {{ Request::is('admin/banks*') ? 'active' : '' }}">
                <a href="{{ route('banks.index') }}"><i class="fas fa-lg fa-university"></i>
                    <!-- Changed icon to fa-building -->
                    <span class="menu-text-wrap">{{ __('messages.banks.name') }}</span>
                    <!-- Updated translation key -->
                </a>
            </li>
        @endcanany

        {{-- <li class="nav-item side-menus {{ Request::is('admin/company-loans*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('company-loans.index') }}">
                <i class="nav-icon fa-lg fas fa-hand-holding-usd"></i>
                <span class="menu-text-wrap">{{ __('messages.company_loans.company_loans') }}</span>
            </a>
        </li>

         <li class="nav-item side-menus {{ Request::is('admin/documents*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('documents.index') }}">
                <i class="nav-icon fa-lg fas fa-file-alt"></i>
                <span class="menu-text-wrap">{{ __('messages.documents.documents') }}</span>
            </a>
        </li>
         <li class="nav-item side-menus {{ Request::is('admin/logged-users*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('logged-users.index') }}">
                <i class="nav-icon fa-lg fas fa-user-clock"></i>
                <span class="menu-text-wrap">{{ __('messages.logged_users.logged_users') }}</span>
            </a>
        </li> --}}
        {{-- Company Loans --}}
        <li class="nav-item side-menus {{ Request::is('project-calculations*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('project-calculations.index') }}">
                <i class="nav-icon fa-lg fas fa-calculator"></i>
                <span class="menu-text-wrap">{{ __('messages.project_calculations.project_calculations') }}</span>
            </a>
        </li>

        {{-- <li class="nav-item side-menus {{ Request::is('safety-materials*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('safety-materials.index') }}">
                <i class="nav-icon fa-lg fas fa-hard-hat"></i>
                <span class="menu-text-wrap">{{ __('messages.safety_materials.safety_materials') }}</span>
            </a>
        </li> --}}
        @canany(['view_safety_materials', 'create_safety_materials', 'update_safety_materials',
            'delete_safety_materials'])
            <li class="nav-item side-menus {{ Request::is('safety-materials*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('safety-materials.index') }}">
                    <i class="nav-icon fa-lg fas fa-hard-hat"></i>
                    <span class="menu-text-wrap">{{ __('messages.safety_materials.safety_materials') }}</span>
                </a>
            </li>
        @endcanany
        @canany(['view_company_loans', 'create_company_loans', 'update_company_loans', 'delete_company_loans'])
            <li class="nav-item side-menus {{ Request::is('admin/company-loans*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('company-loans.index') }}">
                    <i class="nav-icon fa-lg fas fa-hand-holding-usd"></i>
                    <span class="menu-text-wrap">{{ __('messages.company_loans.company_loans') }}</span>
                </a>
            </li>
        @endcanany

        {{-- Documents --}}
        @canany(['view_documents', 'create_documents', 'update_documents', 'delete_documents'])
            <li class="nav-item side-menus {{ Request::is('admin/documents*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('documents.index') }}">
                    <i class="nav-icon fa-lg fas fa-file-alt"></i>
                    <span class="menu-text-wrap">{{ __('messages.documents.documents') }}</span>
                </a>
            </li>
        @endcanany

        {{-- Logged Users --}}
        @canany(['view_logged_users', 'force_logout_logged_users'])
            <li class="nav-item side-menus {{ Request::is('admin/logged-users*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('logged-users.index') }}">
                    <i class="nav-icon fa-lg fas fa-user-clock"></i>
                    <span class="menu-text-wrap">{{ __('messages.logged_users.logged_users') }}</span>
                </a>
            </li>
        @endcanany
        @canany(['view_users', 'create_users', 'update_users', 'delete_users'])
            <li class="side-menus {{ Request::is('admin/members*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('members.index') }}"><i class="fas fa-lg fa-user-friends"></i>
                    <span class="menu-text-wrap">{{ __('messages.members') }}</span>
                </a>
            </li>
        @endcanany

        @canany(['create_backup', 'download_backup', 'delete_backup'])
            <li class="side-menus {{ Request::is('admin/backup*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('backup.index') }}">
                    <i class="fas fa-database fa-lg"></i>
                    <span class="menu-text-wrap">{{ __('messages.backup.name') }}</span>
                </a>
            </li>
        @endcanany
        @can('manage_services')
            <li class="side-menus {{ Request::is('admin/services*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('services.index') }}">
                    <i class="fab fa-lg fa-stripe-s"></i>
                    <span class="menu-text-wrap">{{ __('messages.services') }}</span>
                </a>
            </li>
        @endcan



        @can('manage_activity_logs')
            <li class="side-menus {{ Request::is('admin/activity-logs*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('activity.logs.index') }}">
                    <i class="fas fa-clipboard-check fa-lg" aria-hidden="true"></i>
                    <span>{{ __('messages.activity_log.activity_logs') }}</span>
                </a>
            </li>
        @endcan







        @canany(['view_cities', 'create_cities', 'update_cities', 'delete_cities'])
            <li class="side-menus {{ Request::is('admin/cities*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('cities.index') }}">
                    <i class="fas fa-lg fa-city"></i> <!-- Changed icon to city -->
                    <span class="menu-text-wrap">{{ __('messages.cities.cities') }}</span>
                </a>
            </li>
        @endcanany



        @canany(['view_areas', 'create_areas', 'update_areas', 'delete_areas'])
            <li class="side-menus {{ Request::is('admin/areas*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('areas.index') }}">
                    <i class="fas fa-lg fa-border-style"></i> <!-- Icon for area -->
                    <span class="menu-text-wrap">{{ __('messages.areas.areas') }}</span>
                </a>
            </li>
        @endcanany
        @canany(['manage_fiscal_year'])
            <li class="side-menus {{ Request::is('admin/financial-year') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('financial-year.index') }}">
                    <i class="fas fa-calendar-alt fa-lg"></i>
                    <span class="menu-text-wrap">{{ __('messages.financial_year.name') }}</span>
                </a>
            </li>
        @endcanany
        @canany(['manage_closing_year'])
            <li class="side-menus {{ Request::is('admin/financial-year-ending*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('financial-year.ending') }}">
                    <i class="fas fa-check-circle fa-lg"></i>
                    <span class="menu-text-wrap">{{ __('messages.financial_year.ending') }}</span>
                </a>
            </li>
        @endcanany
        {{-- @canany(['restore', 'view_restore'])
            <li class="side-menus {{ Request::is('admin/restore*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('restore.index') }}">
                    <i class="fas fa-sync-alt fa-lg"></i>
                    <span class="menu-text-wrap">Database Restore</span>
                </a>
            </li>
        @endcanany --}}

        {{-- <li class="side-menus {{ Request::is('admin/translation-manager*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('translation-manager.index') }}">
                <i class="fas fa-language"></i>
                <span>{{ __('messages.translation_manager') }}</span>
            </a>
        </li> --}}
        {{-- sample start here --}}

        @canany(['view_sample_categories', 'create_sample_categories', 'update_sample_categories',
            'delete_sample_categories', 'view_sample_receiving', 'create_sample_receiving', 'update_sample_receiving',
            'delete_sample_receiving'])
            <li class="nav-item dropdown side-menus">
                <a class="nav-link has-dropdown" href="#"><i class="fas fa-lg fa-industry"></i>
                    <span>{{ __('messages.sample.menu') }}</span>
                </a>
                <ul class="dropdown-menu side-menus">
                    @canany(['view_sample_categories', 'create_sample_categories', 'update_sample_categories',
                        'delete_sample_categories'])
                        <li class="side-menus {{ Request::is('admin/sample_categories*') ? 'active   submenu' : '' }}">
                            <a href="{{ route('sample_categories.index') }}"><i class="fas fa-lg fa-bars"></i>
                                <span class="menu-text-wrap">{{ __('messages.sample.sample_categories') }}</span></a>
                        </li>
                    @endcanany
                    @canany(['view_sample_receiving', 'create_sample_receiving', 'update_sample_receiving',
                        'delete_sample_receiving'])
                        <li class="side-menus {{ Request::is('admin/sample_receiving*') ? 'active   submenu' : '' }}">
                            <a href="{{ route('sample_receiving.index') }}"><i class="fas fa-lg fa-industry"></i>
                                <span class="menu-text-wrap">{{ __('messages.sample.sample_receiving') }}</span></a>
                        </li>
                    @endcanany
                </ul>
            </li>
        @endcanany
        {{-- simple End here --}}
        {{-- Certificate Start here --}}
        @canany(['view_certificate', 'create_certificate', 'update_certificate', 'delete_certificate'])
            <li class="side-menus {{ Request::is('admin/certificate*') ? 'active   submenu' : '' }}">
                <a href="{{ route('certificate.index') }}"><i class="fas fa-lg fa-certificate"></i>
                    <span class="menu-text-wrap">{{ __('messages.certificate.menu') }}</span></a>
            </li>
        @endcanany
        {{-- Certificate End here --}}
    </ul>
    <br><br>
</aside>

<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script src="{{ mix('assets/js/sidebar-menu-search/sidebar-menu-search.js') }}"></script>
