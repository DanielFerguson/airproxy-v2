<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title inertia>Airproxy | @yield('title')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">

    <!-- Google Tag Manager -->
    <script>(function (w, d, s, l, i) {
            w[l] = w[l] || []; w[l].push({
                'gtm.start':
                    new Date().getTime(), event: 'gtm.js'
            }); var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : ''; j.async = true; j.src =
                    'https://www.googletagmanager.com/gtm.js?id=' + i + dl; f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', 'GTM-58H5HW4');</script>
    <!-- End Google Tag Manager -->

    <style>

    </style>
</head>

<body class="font-sans antialiased">
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-58H5HW4" height="0" width="0"
            style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

    <!-- Header -->
    <div className="mx-auto max-w-7xl px-6 pt-6 lg:px-8">
        <div>
            <nav className="flex h-9 items-center justify-between" aria-label="Global">
                <div className="flex lg:min-w-0 lg:flex-1" aria-label="Global">
                    <Link href="#" className="-m-1.5 p-1.5">
                    <span className="sr-only">Airproxy</span>
                    <img src="/cloud.png" alt="Airproxy" className="h-16 w-16" />
                    </Link>
                </div>
                <div className="flex lg:hidden">
                    <button type="button"
                        className="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-gray-700"
                        onClick={()=> setMobileMenuOpen(true)}
                        >
                        <span className="sr-only">
                            Open main menu
                        </span>
                        <Bars3Icon className="h-6 w-6" aria-hidden="true" />
                    </button>
                </div>
                <div className="hidden lg:flex lg:min-w-0 lg:flex-1 lg:justify-center lg:gap-x-12">
                    {navigation.map((item) => (
                    <Link key={item.name} href={item.href} className="font-semibold text-gray-900 hover:text-gray-900">
                    {item.name}
                    </Link>
                    ))}
                </div>
                <div className="hidden lg:flex lg:min-w-0 lg:flex-1 lg:justify-end space-x-3">
                    {props.auth.user ? (
                    <Link href={route("dashboard")}
                        className="inline-block rounded-lg px-3 py-1.5 text-sm font-semibold leading-6 text-gray-900 shadow-sm ring-1 ring-gray-900/10 hover:ring-gray-900/20">
                    Dashboard
                    </Link>
                    ) : (
                    <>
                        <Link href={route("register")}
                            className="inline-block rounded-lg px-3 py-1.5 text-sm leading-6 text-gray-900">
                        Register
                        </Link>
                        <Link href={route("login")}
                            className="inline-block rounded-lg px-3 py-1.5 text-sm font-semibold leading-6 text-gray-900 shadow-sm ring-1 ring-gray-900/10 hover:ring-gray-900/20">
                        Log in
                        </Link>
                    </>
                    )}
                </div>
            </nav>
            <!-- <Dialog as="div" open={mobileMenuOpen} onClose={setMobileMenuOpen}>
                <Dialog.Panel className="fixed inset-0 z-10 overflow-y-auto bg-white px-6 py-6 lg:hidden">
                    <div className="flex h-9 items-center justify-between">
                        <div className="flex">
                            <Link href="#" className="-m-1.5 p-1.5">
                            <span className="sr-only">
                                Airproxy
                            </span>
                            <img src={Vapor.asset("cloud.png")} alt="Airproxy" className="h-16 w-16" />
                            </Link>
                        </div>
                        <div className="flex">
                            <button type="button"
                                className="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-gray-700"
                                onClick={()=>
                                setMobileMenuOpen(false)
                                }
                                >
                                <span className="sr-only">
                                    Close menu
                                </span>
                                <XMarkIcon className="h-6 w-6" aria-hidden="true" />
                            </button>
                        </div>
                    </div>
                    <div className="mt-6 flow-root">
                        <div className="-my-6 divide-y divide-gray-500/10">
                            <div className="space-y-2 py-6">
                                {navigation.map((item) => (
                                <a key={item.name} href={item.href}
                                    className="-mx-3 block rounded-lg py-2 px-3 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-400/10">
                                    {item.name}
                                </a>
                                ))}
                            </div>
                            <div className="py-6">
                                {props.auth.user ? (
                                <a href={route("dashboard")}
                                    className="-mx-3 block rounded-lg py-2.5 px-3 text-base font-semibold leading-6 text-gray-900 hover:bg-gray-400/10">
                                    Dashboard
                                </a>
                                ) : (
                                <a href={route("login")}
                                    className="-mx-3 block rounded-lg py-2.5 px-3 text-base font-semibold leading-6 text-gray-900 hover:bg-gray-400/10">
                                    Log in
                                </a>
                                )}
                            </div>
                        </div>
                    </div>
                </Dialog.Panel>
            </Dialog> -->
        </div>
    </div>

    @yield('content')
</body>

</html>