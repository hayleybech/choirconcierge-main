import React from 'react';
import TableMobile, {TableMobileItem} from "../../components/TableMobile";
import useRoute from "../../hooks/useRoute";
import Pagination from '../../components/Pagination';
import Badge from '../../components/Badge';

const RiserStackTableMobile = ({ stacks, userEnsemblesCount }) => {
    const { route } = useRoute();

    return (
		<TableMobile pagination={<Pagination details={stacks} />}>
			{stacks.data.map(stack => (
				<TableMobileItem key={stack.id} url={route('stacks.show', { stack: stack.id })}>
					<div className="min-w-0 flex-1 px-4 lg:grid lg:grid-cols-2 lg:gap-4">
						<div>
							<div className="flex items-center justify-between">
								<p className="flex items-center min-w-0 mr-1.5">
									<span className="text-sm font-medium text-purple-600 truncate">{stack.title}</span>
								</p>
							</div>
						</div>
						{userEnsemblesCount > 1 && stack.ensembles.length > 0 && (
							<div className="mt-2 flex gap-1 flex-wrap">
								{stack.ensembles.map(ensemble => (
									<Badge colour="bg-purple-100 text-purple-800 truncate" display="">
										{ensemble.name}
									</Badge>
								))}
							</div>
						)}
					</div>
				</TableMobileItem>
			))}
		</TableMobile>
	);
}

export default RiserStackTableMobile;