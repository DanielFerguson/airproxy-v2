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
                                src={Vapor.asset("cloud.png")}
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
                                            Home
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
                                <Menu.Item>
                                    {({ active }) => (
                                        <Link
                                            href="docs.airproxy.app"
                                            target="_blank"
                                            className={`block px-4 py-2 text-sm text-gray-700 ${
                                                active ? "bg-gray-100" : ""
                                            }`}
                                        >
                                            Documentation
                                        </Link>
                                    )}
                                </Menu.Item>
                                <Menu.Item>
                                    {({ active }) => (
                                        <Link
                                            href="/profile"
                                            className={`block px-4 py-2 text-sm text-gray-700 ${
                                                active ? "bg-gray-100" : ""
                                            }`}
                                        >
                                            Settings
                                        </Link>
                                    )}
                                </Menu.Item>
                                <Menu.Item>
                                    {({ active }) => (
                                        <Link
                                            href="/logout"
                                            method="post"
                                            as="button"
                                            type="button"
                                            className="block px-4 py-2 text-sm text-gray-700"
                                        >
                                            Logout
                                        </Link>
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
