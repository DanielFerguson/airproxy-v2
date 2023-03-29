import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, useForm } from "@inertiajs/react";
import millify from "millify";
import {
    LockClosedIcon,
    LockOpenIcon,
    SignalIcon,
    SignalSlashIcon,
    CheckIcon,
    KeyIcon,
    PlusIcon,
} from "@heroicons/react/24/outline";
import toast, { Toaster } from "react-hot-toast";
import {
    Text,
    Title,
    Card,
    Flex,
    Metric,
    Grid,
    Table,
    TableHead,
    TableRow,
    TableHeaderCell,
    TableBody,
    TableCell,
    Badge,
    AreaChart,
    TextInput,
    Button,
} from "@tremor/react";
import { Fragment, useEffect, useState } from "react";
import { Dialog, Transition } from "@headlessui/react";

export default function Dashboard({
    auth,
    errors,
    bases,
    stats,
    requests,
    flash,
}) {
    const [open, setOpen] = useState(stats.api_tokens_count === 0);
    const {
        data,
        setData,
        post,
        processing,
        errors: formErrors,
    } = useForm("AddKey", {
        key: "",
    });

    useEffect(() => {
        if (flash.success === "Successfully created API token") {
            toast.success("Successfully created API token");
            setOpen(false);
        }
    }, [flash]);

    function submit(e) {
        e.preventDefault();
        post("/tokens");
    }

    return (
        <AuthenticatedLayout
            auth={auth}
            errors={errors}
            header={
                <h2 className="font-semibold text-xl text-gray-800 leading-tight">
                    Airproxy
                </h2>
            }
        >
            {/* api_tokens_count */}
            <Head title="Dashboard" />
            <Toaster />

            <Transition.Root show={open} as={Fragment}>
                <Dialog
                    as="div"
                    className="relative z-10"
                    onClose={() => console.log("Hello")}
                >
                    <Transition.Child
                        as={Fragment}
                        enter="ease-out duration-300"
                        enterFrom="opacity-0"
                        enterTo="opacity-100"
                        leave="ease-in duration-200"
                        leaveFrom="opacity-100"
                        leaveTo="opacity-0"
                    >
                        <div className="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
                    </Transition.Child>

                    <div className="fixed inset-0 z-10 overflow-y-auto">
                        <div className="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                            <Transition.Child
                                as={Fragment}
                                enter="ease-out duration-300"
                                enterFrom="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                enterTo="opacity-100 translate-y-0 sm:scale-100"
                                leave="ease-in duration-200"
                                leaveFrom="opacity-100 translate-y-0 sm:scale-100"
                                leaveTo="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                            >
                                <Dialog.Panel className="relative transform overflow-hidden rounded-lg bg-white px-4 pt-5 pb-4 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-sm sm:p-6">
                                    <div>
                                        <div className="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-indigo-100">
                                            <PlusIcon
                                                className="h-6 w-6 text-indigo-600"
                                                aria-hidden="true"
                                            />
                                        </div>
                                        <div className="mt-3 text-center sm:mt-5">
                                            <Dialog.Title
                                                as="h3"
                                                className="text-base font-semibold leading-6 text-gray-900"
                                            >
                                                Add your key
                                            </Dialog.Title>
                                            <div className="mt-2 grid space-y-6">
                                                <p className="text-sm text-gray-700">
                                                    Get started by adding your
                                                    Airtable Personal key,{" "}
                                                    <a
                                                        href="https://airtable.com/create/tokens"
                                                        target="_blank"
                                                        rel="noreferrer"
                                                        className="text-indigo-700"
                                                    >
                                                        available here.
                                                    </a>
                                                </p>

                                                <div className="grid space-y-2">
                                                    <TextInput
                                                        icon={KeyIcon}
                                                        placeholder="Add your personal access key..."
                                                        value={data.key}
                                                        error={
                                                            formErrors.key ||
                                                            flash.error
                                                        }
                                                        onChange={(e) =>
                                                            setData(
                                                                "key",
                                                                e.target.value
                                                            )
                                                        }
                                                        errorMessage={
                                                            formErrors.key ||
                                                            flash.error
                                                        }
                                                    />
                                                </div>

                                                <p className="text-sm text-gray-500">
                                                    The token requires the{" "}
                                                    <span className="inline-flex items-center rounded bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-800">
                                                        data.records:read
                                                    </span>
                                                    ,{" "}
                                                    <span className="inline-flex items-center rounded bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-800">
                                                        schema.bases:read
                                                    </span>
                                                    , and the{" "}
                                                    <span className="inline-flex items-center rounded bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-800">
                                                        webhook:manage
                                                    </span>{" "}
                                                    permissions. We suggest
                                                    setting permissions to `All
                                                    current and future bases in
                                                    all current and future
                                                    workspaces`.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div className="mt-5 sm:mt-6">
                                        <Button
                                            size="md"
                                            onClick={submit}
                                            loading={processing}
                                            disabled={processing}
                                            color="indigo"
                                            className="w-full"
                                        >
                                            Add key
                                        </Button>
                                    </div>
                                </Dialog.Panel>
                            </Transition.Child>
                        </div>
                    </div>
                </Dialog>
            </Transition.Root>

            <div className="mx-auto max-w-3xl px-4 pb-24 sm:mt-8">
                <Title>Airproxy</Title>
                <Text>
                    See an overview of all your bases, and stats over the last
                    30 days.
                </Text>

                {stats.api_tokens_count === 0 && (
                    <Card className="mt-6">
                        <Title>Add your base</Title>
                        <Text>
                            Live requests over the last hour. Time is in UTC.
                        </Text>
                    </Card>
                )}

                {/* Stats */}
                <Grid numColsMd={3} className="gap-6 mt-6">
                    <Card decoration="top" decorationColor="indigo">
                        <Flex alignItems="start">
                            <Text>Total Requests</Text>
                        </Flex>
                        <Flex
                            justifyContent="start"
                            alignItems="baseline"
                            className="truncate space-x-3"
                        >
                            <Metric>
                                {millify(stats.total_requests, {
                                    precision: 2,
                                })}
                            </Metric>
                            <Text className="truncate">
                                /{" "}
                                {millify(
                                    auth.plan
                                        ? auth.plan.options.max_monthly_requests
                                        : 0
                                )}
                            </Text>
                        </Flex>
                    </Card>
                    <Card decoration="top" decorationColor="indigo">
                        <Flex alignItems="start">
                            <Text>Unique Users</Text>
                        </Flex>
                        <Flex
                            justifyContent="start"
                            alignItems="baseline"
                            className="truncate space-x-3"
                        >
                            <Metric>
                                {millify(stats.unique_users, {
                                    precision: 2,
                                })}
                            </Metric>
                            <Text className="truncate">
                                /{" "}
                                {millify(
                                    auth.plan
                                        ? auth.plan.options
                                              .max_monthly_unique_users
                                        : 0
                                )}
                            </Text>
                        </Flex>
                    </Card>
                </Grid>
                {/* Requests Charts */}
                <Card className="mt-6">
                    <Title>Requests</Title>
                    <Text>
                        Live requests over the last hour. Time is in UTC.
                    </Text>

                    <AreaChart
                        data={requests}
                        categories={["requests"]}
                        index="minute"
                        colors={["indigo"]}
                    />
                </Card>
                {/* Bases */}
                <Card className="mt-6">
                    <Title>Bases</Title>
                    <Text>A list of all the bases, and their controls.</Text>
                    <Table className="mt-5">
                        <TableHead>
                            <TableRow>
                                <TableHeaderCell>Name</TableHeaderCell>
                                <TableHeaderCell>Status</TableHeaderCell>
                                <TableHeaderCell>Access</TableHeaderCell>
                                <TableHeaderCell>Tables</TableHeaderCell>
                            </TableRow>
                        </TableHead>
                        <TableBody>
                            {bases.map((base) => (
                                <TableRow key={base.id}>
                                    <TableCell>
                                        <a
                                            href={`/bases/${base.id}`}
                                            className="text-indigo-600 hover:text-indigo-900"
                                        >
                                            <span className="mr-2">
                                                {base.name}
                                            </span>
                                        </a>
                                    </TableCell>
                                    <TableCell>
                                        <Badge
                                            color={
                                                base.is_active
                                                    ? "emerald"
                                                    : "gray"
                                            }
                                            icon={
                                                base.is_active
                                                    ? SignalIcon
                                                    : SignalSlashIcon
                                            }
                                        >
                                            {base.is_active
                                                ? "Active"
                                                : "Disabled"}
                                        </Badge>
                                    </TableCell>
                                    <TableCell>
                                        <Badge
                                            color={
                                                base.secret
                                                    ? "emerald"
                                                    : "yellow"
                                            }
                                            icon={
                                                base.secret
                                                    ? LockClosedIcon
                                                    : LockOpenIcon
                                            }
                                        >
                                            {base.secret
                                                ? "Protected"
                                                : "Public"}
                                        </Badge>
                                    </TableCell>
                                    <TableCell>
                                        <Text>{base.tables.length} tables</Text>
                                    </TableCell>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                </Card>
            </div>
        </AuthenticatedLayout>
    );
}
