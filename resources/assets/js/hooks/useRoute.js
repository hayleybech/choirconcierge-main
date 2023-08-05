import {usePage} from "@inertiajs/inertia-react";
import route from "ziggy-js";

const useRoute = (withTenant = true) => {
    const { tenant } = usePage().props;

    const _route = (routeName, params, absolute) => {
        try {
            if(!withTenant) {
                return route(routeName, params, absolute);
            }

            return route(routeName, {
                tenant: tenant?.id,
                ...params,
            }, absolute);
        } catch (error) {
            console.error(error);

            return '';
        }
    }

    return {route: _route};
};

export default useRoute;