import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link } from "@inertiajs/react";
import millify from "millify";
import { PauseIcon, ArrowPathIcon } from "@heroicons/react/20/solid";
import {
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
    AreaChart,
    Col,
} from "@tremor/react";

export default function Dashboard({
    auth,
    errors,
    base,
    stats,
    message,
    requests,
    permissions,
}) {
    console.log(permissions);

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
                <Col>
                    <Title>{base.name}</Title>
                    <Text>
                        Interact with tables, and stats over the last 30 days.
                    </Text>
                </Col>

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
                    <Card
                        decoration="top"
                        decorationColor={base.secret ? "emerald" : "red"}
                    >
                        <Text>Protection</Text>
                        <Metric>{base.secret ? "Protected" : "Public"}</Metric>
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

                {/* API Token */}
                <Card className="mt-6">
                    <Flex>
                        <Col>
                            <Title>API Token</Title>
                            <Text>
                                {!permissions.can_create_token
                                    ? "You need a Team or Business subscription in order to create API tokens."
                                    : base.secret
                                    ? "The APIs under this base are protected with an API key."
                                    : "The APIs under this base are unprotected and can be accessed by anyone."}
                            </Text>
                            {!permissions.can_create_token && (
                                <Link href="/billing">
                                    <Text color="indigo">
                                        Upgrade to get access to API keys.
                                    </Text>
                                </Link>
                            )}
                        </Col>
                        {!permissions.can_create_token ? (
                            <Text>
                                <Link
                                    href="/billing?message=Upgrade to Team or Business to be able to secure your API endpoints."
                                    className="underline text-indigo-600"
                                >
                                    Upgrade your plan.
                                </Link>
                            </Text>
                        ) : (
                            <Link href="/token/create">
                                <Button
                                    size="sm"
                                    color="indigo"
                                    importance="secondary"
                                >
                                    Create token
                                </Button>
                            </Link>
                        )}
                        {/* <Link>Create Token</Link> */}
                        {/* <Button
                            disabled={
                                !auth.plan ||
                                auth.plan.name !== "Team" ||
                                auth.plan.name !== "Business"
                            }
                            size="sm"
                            color="indigo"
                            importance="secondary"
                        >
                            {base.data?.apiToken
                                ? "Remove Token"
                                : "Create Token"}
                        </Button> */}
                    </Flex>
                </Card>

                {/* Tables */}
                <Card className="mt-6">
                    <Flex>
                        <Col>
                            <Title>Tables</Title>
                            <Text>
                                A list of all the tables, and their controls.
                            </Text>
                        </Col>
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
