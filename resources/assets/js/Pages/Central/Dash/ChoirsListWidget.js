import React from 'react';
import SectionTitle from "../../../components/SectionTitle";
import Panel from "../../../components/Panel";
import TableMobile, {TableMobileItem} from "../../../components/TableMobile";
import {usePage} from "@inertiajs/react";
import useRoute from "../../../hooks/useRoute";
import Button from "../../../components/inputs/Button";

const ChoirsListWidget = () => {
    const { route } = useRoute();
    const { userChoirs: choirs, user } = usePage().props;

    return (
        <Panel header={<SectionTitle>Your Choirs</SectionTitle>} noPadding>
            {choirs.length > 0 ? (
                <TableMobile>
                    {choirs.map((choir) => (
                        <TableMobileItem url={route('dash', {tenant: choir.id})} key={choir.id}>
                            <img src={choir.logo_url} alt={choir.choir_name} className="max-h-10 w-auto mr-4 shrink" />
                            {/*<div className="text-sm font-medium text-purple-800 shrink-0">{choir.choir_name}</div>*/}

                            {choirs.length > 1 && (
                                <>
                                {user.default_tenant_id !== choir.id
                                    ? (
                                    <Button
                                        variant="secondary"
                                        size="xs"
                                        href={route('central.default-dash.update', {default_dash: choir.id})}
                                        method="put"
                                        className="mr-2"
                                    >
                                        Set as default
                                    </Button>
                                    )
                                    : (
                                    <div>
                                        <span className="text-gray-600 text-sm mr-2">Default Choir</span>
                                        <Button
                                            variant="danger-outline"
                                            size="xs"
                                            href={route('central.default-dash.destroy', {default_dash: choir.id})}
                                            method="delete"
                                            className="mr-2"
                                        >
                                            Unset default
                                        </Button>
                                    </div>
                                    )
                                }
                                </>
                            )}
                        </TableMobileItem>
                    ))}
                </TableMobile>
            ) : (
                <p className="px-4 py-4 sm:px-6">It looks like you haven't joined a choir.</p>
            )}
        </Panel>
    );
}

export default ChoirsListWidget;