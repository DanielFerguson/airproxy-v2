import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link, router } from "@inertiajs/react";
import millify from "millify";
import { PauseIcon, PlayIcon } from "@heroicons/react/20/solid";
import {
    ShareIcon,
    SignalIcon,
    SignalSlashIcon,
} from "@heroicons/react/24/outline";
import { Toaster, toast } from "react-hot-toast";
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
import { useEffect } from "react";

export default function Dashboard({ auth, errors, base, stats, requests }) {
    useEffect(() => {
        const interval = setInterval(() => {
            router.reload({ only: ["base", "stats", "requests", "auth"] });
        }, 2500);

        return () => clearInterval(interval);
    }, []);

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
                            {base.secret ? (
                                <>
                                    <Text>
                                        The APIs under this base are protected
                                        with an API key.
                                    </Text>
                                    <Text>
                                        Read how to use it{" "}
                                        <a
                                            href="https://blog.airproxy.app/fetching-data/accessing-your-data#protection"
                                            target="_blank"
                                            className="text-indigo-600 hover:text-indigo-500"
                                        >
                                            here.
                                        </a>
                                    </Text>
                                </>
                            ) : (
                                <Text>
                                    The APIs under this base are unprotected and
                                    can be accessed by anyone.
                                </Text>
                            )}
                        </Col>
                        {base.secret ? (
                            <Button
                                size="sm"
                                color="indigo"
                                importance="secondary"
                                onClick={() => {
                                    navigator.clipboard.writeText(base.secret);
                                    toast.success(
                                        "Copied API key to clipboard"
                                    );
                                }}
                            >
                                Copy key
                            </Button>
                        ) : (
                            <Link
                                as="button"
                                method="POST"
                                href={route("base.create-token", base.id)}
                            >
                                <Button
                                    size="sm"
                                    color="indigo"
                                    importance="secondary"
                                >
                                    Create token
                                </Button>
                            </Link>
                        )}
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
                                        <Flex className="justify-end space-x-3">
                                            {/* Disable table */}
                                            <Link
                                                href={route(
                                                    "table.toggle",
                                                    table.id
                                                )}
                                                method="POST"
                                                as="button"
                                                className="inline-flex justify-center items-center group focus:outline-none focus:ring-2 focus:ring-offset-2 font-medium text-xs text-indigo-500 bg-transparent hover:text-indigo-700"
                                            >
                                                <Button
                                                    icon={
                                                        table.is_active
                                                            ? PauseIcon
                                                            : PlayIcon
                                                    }
                                                    size="xs"
                                                    color="indigo"
                                                    variant="light"
                                                />
                                            </Link>
                                            {/* Copy API URL */}
                                            <Button
                                                icon={ShareIcon}
                                                size="xs"
                                                color="indigo"
                                                variant="light"
                                                onClick={() => {
                                                    navigator.clipboard.writeText(
                                                        `${window.location.origin}/api/v1/data/${auth.user.uuid}/${base.name}/${table.name}`
                                                    );

                                                    toast.success(
                                                        "Copied API URL to clipboard."
                                                    );
                                                }}
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
