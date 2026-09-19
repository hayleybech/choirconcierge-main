import React from 'react'
import AppHead from "../../../components/AppHead";
import {
	PageHeader,
	PageHeaderBreadcrumbs,
	PageHeaderContent,
	PageHeaderTitle,
} from '../../../components/PageHeader/PageHeader';
import PageTopBar, { PageTopNavigation } from "../../../components/PageTopBar";
import Icon from "../../../components/Icon";
import CentralLayout from "../../../Layouts/CentralLayout";
import useRoute from "../../../hooks/useRoute";
import AccountForm from "../../Account/AccountForm";

const Edit = ({ setSidebarOpen }) => {
    const { route } = useRoute();

    const breadcrumbs = [
        { name: 'Dashboard', url: route('central.dash') },
        { name: 'Edit Profile', url: route('central.account.edit') },
    ];

    return (
        <>
            <AppHead title="Edit Profile" />
            <PageTopBar setSidebarOpen={setSidebarOpen}>
                <PageTopNavigation breadcrumbs={breadcrumbs} />
            </PageTopBar>
            <PageHeader>
                <PageHeaderContent>
                    <PageHeaderBreadcrumbs breadcrumbs={breadcrumbs} />
                    <PageHeaderTitle>
                        <Icon icon="user-edit" type="solid" className="mr-2" /> Edit Profile
                    </PageHeaderTitle>
                </PageHeaderContent>
            </PageHeader>

            <AccountForm postUrl={route('central.account.update')} cancelUrl={route('central.dash')} />
        </>
    );
}

Edit.layout = page => <CentralLayout children={page} />

export default Edit;
