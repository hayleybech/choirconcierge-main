import React from 'react';
import SectionTitle from "../../../components/SectionTitle";
import Panel from "../../../components/Panel";
import TableMobile, {TableMobileItem} from "../../../components/TableMobile";
import {usePage} from "@inertiajs/inertia-react";
import useRoute from "../../../hooks/useRoute";

const ChoirsListWidget = () => {
    const { route } = useRoute();
    const { userChoirs: choirs } = usePage().props;

    return (
        <Panel header={<SectionTitle>Your Choirs</SectionTitle>} noPadding>
            {choirs.length > 0 ? (
                <TableMobile>
                    {choirs.map((choir) => (
                        <TableMobileItem url={route('dash', {tenant: choir.id})} key={choir.id}>
                            <img src={choir.logo_url} alt={choir.choir_name} className="max-h-10 w-auto mr-4 shrink" />
                            {/*<div className="text-sm font-medium text-purple-800 shrink-0">{choir.choir_name}</div>*/}
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