import TenantNotice from "../../components/TenantNotice";
import React from "react";
import {usePage} from "@inertiajs/react";

const TrialAntiSpamNotice = () => {
    const {tenant} = usePage().props;

    if(!tenant.billing_status.onTrial) {
        return null;
    }

    return (
        <TenantNotice variant="danger" cookieId="mail-trial-anti-spam">
            <p>
                To prevent spam, emails are restricted during trial periods.
                You can create and edit mailing lists, but can not send emails.
                If you need to test these features before purchase, please{' '}
                <a
                    href="https://www.choirconcierge.com/contact"
                    target="_blank"
                    className="underline hover:no-underline focus:no-underline"
                >
                    contact us
                </a>
                .
            </p>
        </TenantNotice>
    );
}

export default TrialAntiSpamNotice;