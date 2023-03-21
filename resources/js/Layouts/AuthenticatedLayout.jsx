import { useState } from "react";
import ApplicationLogo from "@/Components/ApplicationLogo";
import Dropdown from "@/Components/Dropdown";
import NavLink from "@/Components/NavLink";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink";
import { Link } from "@inertiajs/react";
import { Menu, Transition } from "@headlessui/react";
import { Fragment } from "react";

export default function Authenticated({ auth, header, children }) {
    const hash = auth.email;

    return (
        <div className="min-h-screen bg-gray-100">
            <header className="mx-auto flex w-full max-w-3xl items-center justify-between px-4 pt-6 md:px-0">
                <div>
                    <Link href="/dashboard">
                        <h1 className="flex items-center gap-2 font-[Chewy] text-2xl text-gray-800">
                            <img
                                src="/cloud.png"
                                alt="Airproxy"
                                className="h-16 w-16"
                            />
                        </h1>
                    </Link>
                </div>

                <div className="flex items-center gap-3">
                    <Menu as="div" className="relative ml-3">
                        <div>
                            <Menu.Button className="flex rounded-full bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-indigo-500">
                                <span className="sr-only">Open user menu</span>
                                <img
                                    className="h-8 w-8 rounded-full"
                                    src={`https://www.gravatar.com/avatar/${hash}`}
                                    alt="User icon"
                                />
                            </Menu.Button>
                        </div>
                        <Transition
                            as={Fragment}
                            enter="transition ease-out duration-100"
                            enterFrom="transform opacity-0 scale-95"
                            enterTo="transform opacity-100 scale-100"
                            leave="transition ease-in duration-75"
                            leaveFrom="transform opacity-100 scale-100"
                            leaveTo="transform opacity-0 scale-95"
                        >
                            <Menu.Items className="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                                <Menu.Item>
                                    {({ active }) => (
                                        <Link
                                            href="/dashboard"
                                            className={`block px-4 py-2 text-sm text-gray-700 ${
                                                active ? "bg-gray-100" : ""
                                            }`}
                                        >
                                            Dashboard
                                        </Link>
                                    )}
                                </Menu.Item>
                                <Menu.Item>
                                    {({ active }) => (
                                        <Link
                                            href="/billing"
                                            className={`block px-4 py-2 text-sm text-gray-700 ${
                                                active ? "bg-gray-100" : ""
                                            }`}
                                        >
                                            Billing
                                        </Link>
                                    )}
                                </Menu.Item>
                                {/* <Menu.Item>
                                    {({ active }) => (
                                        <Link
                                            href="/docs"
                                            target="_blank"
                                            className={`block px-4 py-2 text-sm text-gray-700 ${
                                                active ? "bg-gray-100" : ""
                                            }`}
                                        >
                                            Docs
                                        </Link>
                                    )}
                                </Menu.Item> */}
                                {/* <Menu.Item>
                                    {({ active }) => (
                                        <Link
                                            href="/settings"
                                            className={`block px-4 py-2 text-sm text-gray-700 ${
                                                active ? "bg-gray-100" : ""
                                            }`}
                                        >
                                            Settings
                                        </Link>
                                    )}
                                </Menu.Item> */}
                                <Menu.Item>
                                    {({ active }) => (
                                        <button
                                            onClick={() => {
                                                signOut();
                                            }}
                                            className={`block w-full px-4 py-2 text-left text-sm text-gray-700 ${
                                                active ? "bg-gray-100" : ""
                                            }`}
                                        >
                                            Sign out
                                        </button>
                                    )}
                                </Menu.Item>
                            </Menu.Items>
                        </Transition>
                    </Menu>
                </div>
            </header>

            <main>{children}</main>
        </div>
    );
}

// <div className="min-h-screen bg-gray-100">
//     <nav className="bg-white border-b border-gray-100">
//         <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
//             <div className="flex justify-between h-16">
//                 <div className="flex">
//                     <div className="shrink-0 flex items-center">
//                         <Link href="/">
//                             <ApplicationLogo className="block h-9 w-auto fill-current text-gray-800" />
//                         </Link>
//                     </div>

//                     <div className="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
//                         <NavLink
//                             href={route("dashboard")}
//                             active={route().current("dashboard")}
//                         >
//                             Dashboard
//                         </NavLink>
//                     </div>
//                 </div>

//                 <div className="hidden sm:flex sm:items-center sm:ml-6">
//                     <div className="ml-3 relative">
//                         <Dropdown>
//                             <Dropdown.Trigger>
//                                 <span className="inline-flex rounded-md">
//                                     <button
//                                         type="button"
//                                         className="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150"
//                                     >
//                                         {auth.user.name}

//                                         <svg
//                                             className="ml-2 -mr-0.5 h-4 w-4"
//                                             xmlns="http://www.w3.org/2000/svg"
//                                             viewBox="0 0 20 20"
//                                             fill="currentColor"
//                                         >
//                                             <path
//                                                 fillRule="evenodd"
//                                                 d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
//                                                 clipRule="evenodd"
//                                             />
//                                         </svg>
//                                     </button>
//                                 </span>
//                             </Dropdown.Trigger>

//                             <Dropdown.Content>
//                                 <Dropdown.Link
//                                     href={route("profile.edit")}
//                                 >
//                                     Profile
//                                 </Dropdown.Link>
//                                 <Dropdown.Link href="/billing">
//                                     Billing
//                                 </Dropdown.Link>
//                                 <Dropdown.Link
//                                     href={route("logout")}
//                                     method="post"
//                                     as="button"
//                                 >
//                                     Log Out
//                                 </Dropdown.Link>
//                             </Dropdown.Content>
//                         </Dropdown>
//                     </div>
//                 </div>

//                 <div className="-mr-2 flex items-center sm:hidden">
//                     <button
//                         onClick={() =>
//                             setShowingNavigationDropdown(
//                                 (previousState) => !previousState
//                             )
//                         }
//                         className="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"
//                     >
//                         <svg
//                             className="h-6 w-6"
//                             stroke="currentColor"
//                             fill="none"
//                             viewBox="0 0 24 24"
//                         >
//                             <path
//                                 className={
//                                     !showingNavigationDropdown
//                                         ? "inline-flex"
//                                         : "hidden"
//                                 }
//                                 strokeLinecap="round"
//                                 strokeLinejoin="round"
//                                 strokeWidth="2"
//                                 d="M4 6h16M4 12h16M4 18h16"
//                             />
//                             <path
//                                 className={
//                                     showingNavigationDropdown
//                                         ? "inline-flex"
//                                         : "hidden"
//                                 }
//                                 strokeLinecap="round"
//                                 strokeLinejoin="round"
//                                 strokeWidth="2"
//                                 d="M6 18L18 6M6 6l12 12"
//                             />
//                         </svg>
//                     </button>
//                 </div>
//             </div>
//         </div>

//         <div
//             className={
//                 (showingNavigationDropdown ? "block" : "hidden") +
//                 " sm:hidden"
//             }
//         >
//             <div className="pt-2 pb-3 space-y-1">
//                 <ResponsiveNavLink
//                     href={route("dashboard")}
//                     active={route().current("dashboard")}
//                 >
//                     Dashboard
//                 </ResponsiveNavLink>
//             </div>

//             <div className="pt-4 pb-1 border-t border-gray-200">
//                 <div className="px-4">
//                     <div className="font-medium text-base text-gray-800">
//                         {auth.user.name}
//                     </div>
//                     <div className="font-medium text-sm text-gray-500">
//                         {auth.user.email}
//                     </div>
//                 </div>

//                 <div className="mt-3 space-y-1">
//                     <ResponsiveNavLink href={route("profile.edit")}>
//                         Profile
//                     </ResponsiveNavLink>
//                     <ResponsiveNavLink
//                         method="post"
//                         href={route("logout")}
//                         as="button"
//                     >
//                         Log Out
//                     </ResponsiveNavLink>
//                 </div>
//             </div>
//         </div>
//     </nav>

//     {header && (
//         <header className="bg-white shadow">
//             <div className="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
//                 {header}
//             </div>
//         </header>
//     )}

//     <main>{children}</main>
// </div>
