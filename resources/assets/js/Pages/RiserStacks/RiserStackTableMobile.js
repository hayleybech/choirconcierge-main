import React from 'react';
import TableMobile, {TableMobileItem, TableMobileListItem, TableMobileSelect, TableMobileSelectableLink } from "../../components/TableMobile";
import useRoute from "../../hooks/useRoute";
import Pagination from '../../components/Pagination';
import Badge from '../../components/Badge';
import BulkEditBarMobile from '../../components/BulkEditBarMobile';

const RiserStackTableMobile = ({ stacks, userEnsemblesCount, bulkEdit }) => {
    const { route } = useRoute();

    return (
		<div>
			<BulkEditBarMobile totalItems={stacks.data.length} bulkEdit={bulkEdit} />
			<TableMobile pagination={<Pagination details={stacks} />} bulkEdit={bulkEdit}>
				{stacks.data.map(stack => (
					<TableMobileListItem key={stack.id}>
						<TableMobileSelect bulkEdit={bulkEdit} value={stack.id} />
						<TableMobileSelectableLink
							url={route('stacks.show', { stack })}
							bulkEdit={bulkEdit}
							value={stack.id}
						>
							<div className="min-w-0 flex-1 lg:grid lg:grid-cols-2 lg:gap-4">
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
						</TableMobileSelectableLink>
					</TableMobileListItem>
				))}
			</TableMobile>
		</div>
	);
}

export default RiserStackTableMobile;