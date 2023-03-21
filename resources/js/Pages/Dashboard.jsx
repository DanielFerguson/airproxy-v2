import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head } from "@inertiajs/react";
import millify from "millify";
import { PauseIcon, PlayIcon, ArrowPathIcon } from "@heroicons/react/20/solid";
import {
    KeyIcon,
    LockClosedIcon,
    LockOpenIcon,
    PauseCircleIcon,
    SignalIcon,
    SignalSlashIcon,
} from "@heroicons/react/24/outline";
import toast, { Toaster } from "react-hot-toast";
import {
    Text,
    Title,
    Card,
    Flex,
    Metric,
    Grid,
    Button,
    Table,
    TableHead,
    TableRow,
    TableHeaderCell,
    TableBody,
    TableCell,
    Badge,
} from "@tremor/react";

export default function Dashboard({ auth, errors, bases, stats }) {
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
            <Head title="Dashboard" />
            <Toaster />

            {/* TODO: Workflow to setting up account / 0 bases / no active key */}

            {/* Bases */}
            <div className="mx-auto max-w-3xl px-4 pb-24 sm:mt-8">
                <Title>Airproxy</Title>
                <Text>
                    See an overview of all your bases, and stats over the last
                    30 days.
                </Text>
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
                                    auth.plan.options.max_monthly_requests
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
                                    auth.plan.options.max_monthly_unique_users
                                )}
                            </Text>
                        </Flex>
                    </Card>
                </Grid>
                {/* Requests Charts */}
                {/* <Card className="mt-6">
                    <Title>Requests</Title>
                    <Text>
                        Live requests over the last 30 minutes. Time is in UTC.
                    </Text>

                    <AreaChart
                            data={requests.data}
                            categories={["Requests"]}
                            dataKey="Date"
                            height="h-72"
                            colors={["indigo"]}
                            marginTop="mt-4"
                        />

                    {requests.data && requests.data.length === 0 && (
                            <Callout
                                title="Where are my cool charts, dude?"
                                text="When you start receiving requests, you will be able to monitor them here."
                                icon={InformationCircleIcon}
                                color="yellow"
                                height=""
                                marginTop="mt-5"
                            />
                        )}
                </Card> */}
                {/* Bases */}
                <Card className="mt-6">
                    <Flex>
                        <div>
                            <Title>Bases</Title>
                            <Text>
                                A list of all the bases, and their controls.
                            </Text>
                        </div>
                        <Flex className="space-x-4 justify-end items-end">
                            <Button icon={ArrowPathIcon} color="indigo">
                                Refresh list
                            </Button>
                            <Button icon={PauseCircleIcon} color="indigo">
                                Disable all
                            </Button>
                        </Flex>
                    </Flex>
                    <Table className="mt-5">
                        <TableHead>
                            <TableRow>
                                <TableHeaderCell>Name</TableHeaderCell>
                                <TableHeaderCell>Status</TableHeaderCell>
                                <TableHeaderCell>Access</TableHeaderCell>
                                <TableHeaderCell>Tables</TableHeaderCell>
                                <TableHeaderCell>
                                    <span className="sr-only">Actions</span>
                                </TableHeaderCell>
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
                                        {/* TODO */}
                                        <Text>{base.tables.length} tables</Text>
                                    </TableCell>
                                    <TableCell>
                                        <Flex>
                                            <Button
                                                icon={ArrowPathIcon}
                                                size="xs"
                                                variant="light"
                                                color="indigo"
                                            ></Button>
                                            <Button
                                                icon={
                                                    base.is_active
                                                        ? PauseIcon
                                                        : PlayIcon
                                                }
                                                variant="light"
                                                size="xs"
                                                color="indigo"
                                            />
                                            <Button
                                                disabled={!base.secret}
                                                variant="light"
                                                icon={KeyIcon}
                                                size="xs"
                                                color="indigo"
                                            />
                                        </Flex>
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
