import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head } from "@inertiajs/react";
import millify from "millify";
import { PauseIcon, ArrowPathIcon } from "@heroicons/react/20/solid";
import {
    PauseCircleIcon,
    ShareIcon,
    SignalIcon,
    SignalSlashIcon,
} from "@heroicons/react/24/outline";
import { Toaster } from "react-hot-toast";
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

export default function Dashboard({ auth, errors, base, stats }) {
    return (
        <AuthenticatedLayout
            auth={auth}
            errors={errors}
            header={
                <h2 className="font-semibold text-xl text-gray-800 leading-tight">
                    {base.name}
                </h2>
            }
        >
            <Head title="Dashboard" />
            <Toaster />

            {/* Bases */}
            <div className="mx-auto max-w-3xl px-4 pb-24 sm:mt-8">
                <Title>{base.name}</Title>
                <Text>
                    Interact with tables, and stats over the last 30 days.
                </Text>

                {/* Stats */}
                <Grid numColsMd={3} className="mt-6 gap-6">
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
                    <Card
                        decoration="top"
                        decorationColor={base.secret ? "emerald" : "red"}
                    >
                        <Text>Protection</Text>
                        <Metric>{base.secret ? "Protected" : "Public"}</Metric>
                    </Card>
                </Grid>

                {/* API Token */}
                <Card className="mt-6">
                    <Flex>
                        <div>
                            <Title>API Token</Title>
                            <Text>
                                {auth.plan.name !== "Team" &&
                                auth.plan.name !== "Business"
                                    ? "You need a Team or Business subscription in order to create API tokens."
                                    : base.data?.apiToken
                                    ? "The APIs under this base are protected with an API key."
                                    : "The APIs under this base are unprotected and can be accessed by anyone."}
                            </Text>
                            {auth.plan.name !== "Team" &&
                                auth.plan.name !== "Business" && (
                                    <a href="/billing">
                                        <Text color="indigo">
                                            Upgrade to get access to API keys.
                                        </Text>
                                    </a>
                                )}
                        </div>
                        <Button
                            disabled={
                                auth.plan.name !== "Team" &&
                                auth.plan.name !== "Business"
                            }
                            size="sm"
                            color="indigo"
                            importance="secondary"
                        >
                            {base.data?.apiToken
                                ? "Remove Token"
                                : "Create Token"}
                        </Button>
                    </Flex>
                </Card>

                {/* Tables */}
                <Card className="mt-6">
                    <Flex>
                        <div>
                            <Title>Tables</Title>
                            <Text>
                                A list of all the tables, and their controls.
                            </Text>
                        </div>
                        <Flex className="items-end justify-end space-x-4">
                            <Button icon={PauseCircleIcon} color="indigo">
                                {base.tables.filter((table) => table.is_active)
                                    .length > 0
                                    ? "Disable all"
                                    : "Enable all"}
                            </Button>
                        </Flex>
                    </Flex>
                    <Table className="mt-5">
                        <TableHead>
                            <TableRow>
                                <TableHeaderCell>Name</TableHeaderCell>
                                <TableHeaderCell>Status</TableHeaderCell>
                                <TableHeaderCell>Requests</TableHeaderCell>
                                <TableHeaderCell>TTL</TableHeaderCell>
                                <TableHeaderCell>
                                    <span className="sr-only">Actions</span>
                                </TableHeaderCell>
                            </TableRow>
                        </TableHead>
                        <TableBody>
                            {base.tables.map((table) => (
                                <TableRow key={table.id}>
                                    <TableCell>
                                        <Text>{table.name}</Text>
                                    </TableCell>
                                    <TableCell>
                                        <Text>
                                            <Badge
                                                color={
                                                    table.is_active
                                                        ? "emerald"
                                                        : "gray"
                                                }
                                                icon={
                                                    table.is_active
                                                        ? SignalIcon
                                                        : SignalSlashIcon
                                                }
                                            >
                                                {table.is_active
                                                    ? "Active"
                                                    : "Disabled"}
                                            </Badge>
                                        </Text>
                                    </TableCell>
                                    <TableCell>
                                        <Text>Coming Soon</Text>
                                    </TableCell>
                                    <TableCell>
                                        <Text>Coming Soon</Text>
                                    </TableCell>
                                    <TableCell>
                                        <Flex>
                                            {/* Bust cache */}
                                            <Button
                                                icon={ArrowPathIcon}
                                                size="xs"
                                                color="indigo"
                                                variant="light"
                                            ></Button>
                                            {/* Disable table */}
                                            <Button
                                                icon={PauseIcon}
                                                size="xs"
                                                color="indigo"
                                                variant="light"
                                            ></Button>
                                            {/* Copy API URL */}
                                            <Button
                                                icon={ShareIcon}
                                                size="xs"
                                                color="indigo"
                                                variant="light"
                                            ></Button>
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
