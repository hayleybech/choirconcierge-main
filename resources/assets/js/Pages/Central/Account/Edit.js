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

const Edit = ({ setSidebarOpen, two_factor_enabled, recovery_codes }) => {
    const { route } = useRoute();

    const breadcrumbs = [
        { name: 'Dashboard', url: route('central.dash') },
        { name: 'Account Settings', url: route('central.account.edit') },
    ];

    return (
        <>
            <AppHead title="Account Settings" />
            <PageTopBar setSidebarOpen={setSidebarOpen}>
                <PageTopNavigation breadcrumbs={breadcrumbs} />
            </PageTopBar>
            <PageHeader>
                <PageHeaderContent>
                    <PageHeaderBreadcrumbs breadcrumbs={breadcrumbs} />
                    <PageHeaderTitle>
                        <Icon icon="user-edit" type="solid" className="mr-2" /> Account Settings
                    </PageHeaderTitle>
                </PageHeaderContent>
            </PageHeader>

            <AccountForm postUrl={route('central.account.update')} cancelUrl={route('central.dash')} twoFactorUrl={route('central.account.two-factor.show')} twoFactorEnabled={two_factor_enabled} recoveryCodes={recovery_codes} />
        </>
    );
}

Edit.layout = page => <CentralLayout children={page} />

export default Edit;
