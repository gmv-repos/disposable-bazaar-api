<!--sidebar wrapper -->
<div class="sidebar-wrapper" data-simplebar="true">
    <div class="sidebar-header d-flex justify-content-center">
        <img src="{{ asset('assets/adminPanel/images/Logo.svg') }}" class="logo-icon" alt="logo icon">
        <div>
            <h4 class="logo-text">Disposable Bazaar</h4>
        </div>
        <div class="toggle-icon ms-auto"><i class='bx bx-arrow-to-left'></i>
        </div>
    </div>
    <!--navigation-->
    <ul class="metismenu" id="menu">
        <li>
            <a href="{{ route('admin.pos.view') }}">

                <div class="menu-title">
                    <spna class="add-menu-sidebar" style="display: flex;justify-content: center;align-items: center"
                        data-toggle="modal" data-target="#addOrderModalside">
                        <i class="fa fa-plus"></i>
                        <span class="nav-text text-center text-white"><i class="lni lni-circle-plus"></i>POS</span>
                    </spna>
                </div>
            </a>
        </li>
        {{-- <li class="menu-label">UI Elements</li> --}}
        <li>
            <a href="{{ route('home') }}">
                <div class="parent-icon"><i class='bx bx-home-circle'></i>
                </div>
                <div class="menu-title">Home</div>
            </a>
        </li>
        <li>
            <a href="{{ route('web.orders.index') }}">
                <div class="parent-icon">
                    <i class="lni lni-cart"></i>
                </div>
                <div class="menu-title">Orders</div>
            </a>
        </li>
        {{-- <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class="lni lni-cart"></i>
                </div>
                <div class="menu-title">All Orders</div>
            </a>
            <ul>
                <li>
                    <a href="{{ route('admin.ecommerce.order.list') }}">
                        <i class="bx bx-right-arrow-alt"></i>
                        E-Commerce Orders
                    </a>
                </li>

                <li>
                    <a href="{{ route('sell.list') }}">
                        <i class="bx bx-right-arrow-alt"></i>
                        POS Orders
                    </a>
                </li>
            </ul>
        </li> --}}

        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class="lni lni-wallet"></i></div>
                <div class="menu-title">Vouchers</div>
            </a>
            <ul>
                <li>
                    <a href="{{ route('admin.vouchers.index') }}">
                        <i class="bx bx-right-arrow-alt"></i>
                        Payment / Receipt Voucher
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.vouchers.inOutToAccount') }}">
                        <i class="bx bx-right-arrow-alt"></i>
                        Bank Credit/Debit
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.vouchers.extraTransaction') }}">
                        <i class="bx bx-right-arrow-alt"></i>
                        Extra Transaction
                    </a>
                </li>
                {{-- <li>
                    <a href="{{route('admin.payment.list')}}">
                        <i class="bx bx-right-arrow-alt"></i>
                        Purchase Payment Voucher
                    </a>
                </li>
                <li>
                    <a href="{{route('admin.receipt.list')}}">
                        <i class="bx bx-right-arrow-alt"></i>
                        Sale Receipt Voucher
                    </a>
                </li> --}}
                <li>
                    <a href="{{ route('expenses.index') }}">
                        <i class="bx bx-right-arrow-alt"></i>
                        Expense Voucher
                    </a>
                </li>
                <li>
                    <a href="{{ route('rider.payments.index') }}">
                        <i class="bx bx-right-arrow-alt"></i>
                        Rider Payments
                    </a>
                </li>
            </ul>
        </li>


        @if (userCanAccess('h1'))
            <li>
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon"><i class="lni lni-cart"></i>
                    </div>
                    <div class="menu-title">Admin Role</div>
                </a>
                <ul>
                    @if (userCanAccess('1'))
                        <li>
                            <a href="{{ route('admin.role.create') }}"><i class="bx bx-right-arrow-alt"></i>Role</a>
                        </li>
                    @endif

                    @if (userCanAccess('2'))
                        <li>
                            <a href="{{ route('admin.admin.create') }}"><i class="bx bx-right-arrow-alt"></i>Create
                                Admin</a>
                        </li>
                    @endif
                </ul>
            </li>
        @endif
        <!-- <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class="lni lni-cart"></i>
                </div>
                <div class="menu-title">Admin Role</div>
            </a>
            <ul>
                <li>
                    <a href="{{ route('admin.role.create') }}"><i class="bx bx-right-arrow-alt"></i>Role</a>
                </li>
            </ul>
        </li> -->
        @if (userCanAccess('h2'))
            <li>
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon">
                        <i class="lni lni-cart"></i>
                    </div>
                    <div class="menu-title">POS</div>
                </a>
                <ul>
                    @if (userCanAccess('3'))
                        <li>
                            <a href="{{ route('admin.pos.view') }}"><i class="bx bx-right-arrow-alt"></i>POS</a>
                        </li>
                    @endif
                    @if (userCanAccess('3'))
                        <li>
                            <a href="{{ route('admin.pos.salesOrders') }}">
                                <i class="bx bx-right-arrow-alt"></i>
                                Sale Order
                            </a>
                        </li>
                    @endif
                    @if (userCanAccess('4'))
                        <li>
                            <a href="{{ route('sell.list') }}"><i class="bx bx-right-arrow-alt"></i>Sell List</a>
                        </li>
                    @endif
                    {{-- @if (userCanAccess('5')) --}}
                    {{-- <li>
                        <a href="{{ route('admin.pos.customer.list') }}"><i class="bx bx-right-arrow-alt"></i>Pos
                            Customer List</a>
                    </li> --}}
                    {{-- @endif --}}
                    {{-- <li>
                        <a href="{{route('admin.receipt.list')}}"><i class="bx bx-right-arrow-alt"></i>Sell Receipt List
                        </a>
                    </li> --}}


                </ul>
            </li>
        @endif

        @if (true)
            <li>
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon">
                        <i class="lni lni-cart"></i>
                    </div>
                    <div class="menu-title">Quotations</div>
                </a>
                <ul>
                    <li>
                        <a href="{{ route('quotations.index') }}">
                            <i class="bx bx-right-arrow-alt"></i>
                            Quotations List
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('quotations.create') }}">
                            <i class="bx bx-right-arrow-alt"></i>
                            Create Quotation
                        </a>
                    </li>
                </ul>
            </li>
        @endif

        @if (userCanAccess('h3'))
            <li>
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon"><i class="lni lni-producthunt"></i>
                    </div>
                    <div class="menu-title">Product</div>
                </a>
                <ul>
                    @if (userCanAccess('8'))
                        <li>
                            <a href="{{ route('admin.product.list') }}"><i class="bx bx-right-arrow-alt"></i>Products
                                List</a>
                        </li>
                    @endif
                    @if (userCanAccess('5'))
                        <li>
                            <a href="{{ route('admin.create.product') }}">
                                <i class="bx bx-right-arrow-alt"></i>
                                Add Products
                            </a>
                        </li>
                    @endif
                    @if (userCanAccess('6'))
                        <li><a href="{{ route('admin.product.category') }}"><i
                                    class="bx bx-right-arrow-alt"></i>Category</a>
                        </li>
                    @endif
                    @if (userCanAccess('7'))
                        <li style="display:none;">
                            <a href="{{ route('admin.product.subcategory') }}"><i
                                    class="bx bx-right-arrow-alt"></i>Subcategory</a>
                        </li>
                    @endif
            </li>

            <li>
                <a href="{{ route('admin.product.color.show') }}"><i class="bx bx-right-arrow-alt"></i>Product
                    Color</a>
            </li>
            <li>
                <a href="{{ route('admin.product.size.show') }}"><i class="bx bx-right-arrow-alt"></i>Product
                    Size</a>
            </li>
            <li>
                <a href="{{ route('admin.product.brand') }}"><i class="bx bx-right-arrow-alt"></i>Product
                    Brand</a>
            </li>

            <li>
                <a href="{{ route('admin.product.variants.show') }}"><i class="bx bx-right-arrow-alt"></i>Product
                    Variants</a>
            </li>
            <li>
                <a href="{{ route('admin.product.option.show') }}"><i class="bx bx-right-arrow-alt"></i>Product
                    Options</a>
            </li>
            <li>
                <a href="{{ route('product.lids.index') }}">
                    <i class="bx bx-right-arrow-alt"></i>
                    Product Lid Options
                </a>
            </li>
    </ul>
    </li>
    @endif

    @if (userCanAccess('h8'))
        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class="lni lni-producthunt"></i>
                </div>
                <div class="menu-title">Bundles</div>
            </a>
            <ul>
                @if (userCanAccess('15'))
                    <li>
                        <a href="{{ route('bundles.index') }}"><i class="bx bx-right-arrow-alt"></i>Bundles List</a>
                    </li>
                @endif
                @if (userCanAccess('16'))
                    <li>
                        <a href="{{ route('bundles.create') }}"><i class="bx bx-right-arrow-alt"></i>Add Bundle</a>
                    </li>
                @endif
            </ul>

        </li>
    @endif

    @if (userCanAccess('h8'))
        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class="lni lni-producthunt"></i>
                </div>
                <div class="menu-title">E-Commerce</div>
            </a>
            <ul>
                @if (userCanAccess('15'))
                    <li>
                        <a href="{{ route('admin.ecommerce.order.list') }}"><i
                                class="bx bx-right-arrow-alt"></i>Order List</a>
                    </li>
                @endif
                @if (userCanAccess('16'))
                    <li>
                        <a href="{{ route('admin.ecommerce.customer.list') }}"><i
                                class="bx bx-right-arrow-alt"></i>Customer
                            List</a>
                    </li>
                @endif
            </ul>

        </li>
    @endif
    @if (userCanAccess('h4'))
        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class="lni lni-package"></i>
                </div>
                <div class="menu-title">Purchase</div>
            </a>
            <ul>
                @if (userCanAccess('9'))
                    <li>
                        <a href="{{ route('admin.product.purchase') }}">
                            <i class="bx bx-right-arrow-alt"></i>
                            Create PO
                        </a>
                    </li>
                @endif
                <li>
                    <a href="{{ route('admin.product.purchase.list') }}"><i class="bx bx-right-arrow-alt"></i>POs
                        List
                    </a>
                </li>

                <li>
                    <a href="{{ route('purchase.received.create') }}">
                        <i class="bx bx-right-arrow-alt"></i>
                        Create PR/GRN
                    </a>
                </li>

                <li>
                    <a href="{{ route('purchase.received.index') }}">
                        <i class="bx bx-right-arrow-alt"></i>
                        PR/GRNs List
                    </a>
                </li>

                {{-- <li>
                    <a href="{{route('admin.payment.list')}}">
                        <i class="bx bx-right-arrow-alt"></i>
                        Purchase Payment List
                    </a>
                </li> --}}


                {{-- @if (userCanAccess('9')) --}}
                <li>
                    <a href="{{ route('admin.supplier.list') }}"><i class="bx bx-right-arrow-alt"></i>Supplier
                        List</a>
                </li>
                {{-- @endif --}}
            </ul>
        </li>
    @endif
    @if (userCanAccess('h5'))
        <li style="display:none;">
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class="lni lni-offer"></i>
                </div>
                <div class="menu-title">Offer Setting</div>
            </a>
            <ul>
                @if (userCanAccess('10'))
                    <li>
                        <a href="{{ route('offer.list') }}"><i class="bx bx-right-arrow-alt"></i>Create Offer</a>
                    </li>
                @endif
                {{-- <li> --}}
                {{-- <a href="{{route('admin.set.offer.product')}}"><i class="bx bx-right-arrow-alt"></i>Offer Product
                        Select</a> --}}
                {{-- </li> --}}
                {{-- @if (userCanAccess('11')) --}}
                {{-- <li> --}}
                {{-- <a href="{{route('admin.offer.product.list')}}"><i class="bx bx-right-arrow-alt"></i>Offer --}}
                {{-- Product --}}
                {{-- List</a> --}}
                {{-- </li> --}}
                {{-- @endif --}}
            </ul>
        </li>
    @endif
    @if (userCanAccess('h6'))
        <li style="display:none;">
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class="lni lni-offer"></i>
                </div>
                <div class="menu-title">Setting</div>
            </a>
            <ul>
                @if (userCanAccess('12'))
                    <li>
                        <a href="{{ route('setting.company.details') }}"><i class="bx bx-right-arrow-alt"></i>Company
                            Details</a>
                    </li>
                @endif
                @if (userCanAccess('12'))
                    <li>
                        <a href="{{ route('setting.shipping.rate') }}"><i class="bx bx-right-arrow-alt"></i>Shipping
                            Rate Set</a>
                    </li>
                @endif
                <li>
                    <a href="{{ route('faq.view') }}"><i class="bx bx-right-arrow-alt"></i>
                        FAQ Set</a>
                </li>
                <li>
                    <a href="{{ route('ads.view') }}"><i class="bx bx-right-arrow-alt"></i>
                        Ads Set</a>
                </li>


            </ul>
        </li>
    @endif
    <li style="display:none;">
        <a href="javascript:;" class="has-arrow">
            <div class="parent-icon"><i class="lni lni-offer"></i>
            </div>
            <div class="menu-title">Featured Link</div>
        </a>
        <ul>
            <li>
                <a href="{{ route('admin.featured.link.list') }}"><i class="bx bx-right-arrow-alt"></i> Featured Link
                    List </a>
            </li>
        </ul>
    </li>
    @if (userCanAccess('h7'))
        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class="lni lni-home"></i>
                </div>
                <div class="menu-title">Bank</div>
            </a>
            <ul>
                <li>
                    <a href="{{ route('admin.bank.list') }}"><i class="bx bx-right-arrow-alt"></i>Bank List</a>
                </li>
            </ul>
        </li>
    @endif
    <li>
        <a href="javascript:;" class="has-arrow">
            <div class="parent-icon"><i class="lni lni-home"></i>
            </div>
            <div class="menu-title">Cash</div>
        </a>
        <ul>
            <li>
                <a href="{{ route('admin.cash.list') }}"><i class="bx bx-right-arrow-alt"></i>Cash List</a>
            </li>
        </ul>
    </li>
    @if (userCanAccess('h7'))
        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class="lni lni-home"></i>
                </div>
                <div class="menu-title">Expenses</div>
            </a>
            <ul>
                <li>
                    <a href="{{ route('expense.expenseAccountIndex') }}">
                        <i class="bx bx-right-arrow-alt"></i>
                        Expenses Account List
                    </a>
                </li>
                <li>
                    <a href="{{ route('expenses.expense-account-create') }}">
                        <i class="bx bx-right-arrow-alt"></i>
                        Expenses Account
                    </a>
                </li>
                <li>
                    <a href="{{ route('expenses.index') }}">
                        <i class="bx bx-right-arrow-alt"></i>
                        Expenses List
                    </a>
                </li>
                <li>
                    <a href="{{ route('expenses.create') }}">
                        <i class="bx bx-right-arrow-alt"></i>
                        Add Expense
                    </a>
                </li>
            </ul>
        </li>
    @endif

    @if (true)
        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class="lni lni-home"></i>
                </div>
                <div class="menu-title">Parties</div>
            </a>
            <ul>
                <li>
                    <a href="{{ route('admin.parties.index') }}"><i class="bx bx-right-arrow-alt"></i>Parties
                        List</a>
                </li>
                <li>
                    <a href="{{ route('admin.parties.create') }}"><i class="bx bx-right-arrow-alt"></i>Add Party</a>
                </li>
            </ul>
        </li>
    @endif

    @if (userCanAccess('h9'))
        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class="lni lni-stats-up"></i>
                </div>
                <div class="menu-title">Report</div>
            </a>
            <ul>
                @if (true)
                    <li>
                        <a href="{{ route('admin.report.simple-stock-report') }}">
                            <i class="bx bx-right-arrow-alt"></i>
                            Simple Stock Report
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.report.detail-stock-report') }}">
                            <i class="bx bx-right-arrow-alt"></i>
                            Detail Stock Report
                        </a>
                    </li>
                @endif
                @if (true)
                    <li>
                        <a href="{{ route('admin.report.stock-summary-report') }}">
                            <i class="bx bx-right-arrow-alt"></i>
                            Stock Summary Report
                        </a>
                    </li>
                @endif
                @if (true)
                    <li>
                        <a href="{{ route('admin.report.purchase') }}">
                            <i class="bx bx-right-arrow-alt"></i>
                            Purchase Report
                        </a>
                    </li>
                @endif
                @if (true)
                    <li>
                        <a href="{{ route('admin.report.sellAndOrderReport') }}">
                            <i class="bx bx-right-arrow-alt"></i>
                            Sale Report
                        </a>
                    </li>
                @endif
                {{-- @if (true)
                <li>
                    <a href="{{route('admin.report.sell.order')}}">
                        <i class="bx bx-right-arrow-alt"></i>
                        Sell (POS) Report
                    </a>
                </li>
                @endif
                @if (true)
                <li>
                    <a href="{{route('admin.report.order')}}">
                        <i class="bx bx-right-arrow-alt"></i>
                        Order Report
                    </a>
                </li>
                @endif --}}
                {{-- @if (true)
                <li>
                    <a href="{{route('admin.report.brand')}}">
                        <i class="bx bx-right-arrow-alt"></i>
                        Brand Report
                    </a>
                </li>
                @endif --}}
                @if (userCanAccess('23'))
                    <li>
                        <a href="{{ route('admin.report.sell.profit') }}"><i class="bx bx-right-arrow-alt"></i>Sell &
                            Profit Report</a>
                    </li>
                @endif
                @if (userCanAccess('22'))
                    <li>
                        <a href="{{ route('admin.report.sell') }}"><i class="bx bx-right-arrow-alt"></i>Best Sell
                            Product Report</a>
                    </li>
                @endif
                @if (userCanAccess('26'))
                    <li>
                        <a href="{{ route('admin.report.partyLedgerReport') }}">
                            <i class="bx bx-right-arrow-alt"></i>
                            Party Ledger
                        </a>
                    </li>
                @endif
                @if (userCanAccess('26'))
                    <li>
                        <a href="{{ route('admin.report.customerLedger') }}"><i
                                class="bx bx-right-arrow-alt"></i>Customer
                            Ledger</a>
                    </li>
                @endif
                @if (userCanAccess('27'))
                    <li>
                        <a href="{{ route('admin.report.supplierLedger') }}"><i
                                class="bx bx-right-arrow-alt"></i>Supplier
                            Ledger</a>
                    </li>
                @endif
                <li>
                    <a href="{{ route('admin.report.bankLedger') }}"><i class="bx bx-right-arrow-alt"></i>Bank
                        Ledger</a>
                </li>
                <li>
                    <a href="{{ route('admin.report.cashLedger') }}"><i class="bx bx-right-arrow-alt"></i>Cash
                        Ledger</a>
                </li>

            </ul>
        </li>
    @endif
    @if (userCanAccess('h10'))
        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class="lni lni-home"></i>
                </div>
                <div class="menu-title">Blogs</div>
            </a>
            <ul>
                @if (userCanAccess('24'))
                    <li>
                        <a href="{{ route('blogs.create') }}"><i class="bx bx-right-arrow-alt"></i>Add New Blog</a>
                    </li>
                @endif
                @if (userCanAccess('25'))
                    <li>
                        <a href="{{ route('blogs.list') }}"><i class="bx bx-right-arrow-alt"></i>Blogs List</a>
                    </li>
                @endif
            </ul>
        </li>
    @endif


    @if (true)
        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class="lni lni-area"></i>
                </div>
                <div class="menu-title">Area</div>
            </a>
            <ul>
                @if (userCanAccess('24'))
                    <li>
                        <a href="{{ route('areas.create') }}"><i class="bx bx-right-arrow-alt"></i>Add New Area</a>
                    </li>
                @endif
                @if (userCanAccess('25'))
                    <li>
                        <a href="{{ route('areas.list') }}"><i class="bx bx-right-arrow-alt"></i>Areas List</a>
                    </li>
                @endif
            </ul>
        </li>
    @endif
    @if (userCanAccess('h12'))
        <li>
            <a href="{{ route('contact.us') }}">
                <div class="parent-icon"><i class='bx bx-contact-circle'></i>
                </div>
                <div class="menu-title">Contact Us</div>
            </a>
        </li>
    @endif

    @if (true)
        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class="lni lni-book"></i>
                </div>
                <div class="menu-title">Pages</div>
            </a>
            <ul>
                <li>
                    <a href="{{ route('page.create') }}"><i class="bx bx-right-arrow-alt"></i>Add New Page</a>
                </li>
                <li>
                    <a href="{{ route('pages.list') }}"><i class="bx bx-right-arrow-alt"></i>Pages List</a>
                </li>
            </ul>
        </li>
    @endif

    @if (true)
        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon">
                    <i class="lni lni-tag"></i>
                </div>
                <div class="menu-title">Discounts</div>
            </a>
            <ul>
                <li>
                    <a href="{{ route('discounts.create') }}"><i class="bx bx-right-arrow-alt"></i>
                        Add Discount
                    </a>
                </li>
                <li>
                    <a href="{{ route('discounts.index') }}"><i class="bx bx-right-arrow-alt"></i>
                        Discount List
                    </a>
                </li>
            </ul>
        </li>
    @endif

    @if (true)
        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon">
                    <i class="lni lni-delivery"></i>
                </div>
                <div class="menu-title">Riders</div>
            </a>
            <ul>
                <li>
                    <a href="{{ route('riders.create') }}"><i class="bx bx-right-arrow-alt"></i>
                        Add Rider
                    </a>
                </li>
                <li>
                    <a href="{{ route('riders.index') }}"><i class="bx bx-right-arrow-alt"></i>
                        Riders List
                    </a>
                </li>
            </ul>
        </li>
    @endif

    </ul>
    <!--end navigation-->
</div>
<!--end sidebar wrapper -->
