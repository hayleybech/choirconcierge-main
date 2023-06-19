import React from 'react';
import TableMobile, {TableMobileItem} from "../../components/TableMobile";
import useRoute from "../../hooks/useRoute";

const RiserStackTableMobile = ({ stacks }) => {
    const { route } = useRoute();

    return (
        <TableMobile>
            {stacks.map((stack) => (
                <TableMobileItem key={stack.id} url={route('stacks.show', {stack: stack.id})}>
                    <div className="min-w-0 flex-1 px-4 lg:grid lg:grid-cols-2 lg:gap-4">
                        <div>
                            <div className="flex items-center justify-between">
                                <p className="flex items-center min-w-0 mr-1.5">
                                    <span className="text-sm font-medium text-purple-600 truncate">{stack.title}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </TableMobileItem>
            ))}
        </TableMobile>
    );
}

export default RiserStackTableMobile;