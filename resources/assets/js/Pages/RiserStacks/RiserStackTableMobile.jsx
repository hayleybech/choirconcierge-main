import React from 'react';
 
import TableMobile, {
	TableMobileListItem,
	TableMobileSelect,
	TableMobileSelectableLink,
} from '../../components/TableMobile';
import useRoute from '../../hooks/useRoute';
import Pagination from '../../components/Pagination';
import EnsembleBadges from '../../components/EnsembleBadges';

const RiserStackTableMobile = ({ stacks, userEnsemblesCount, bulkEdit }) => {
	const { route } = useRoute();

	return (
		<div>
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
											<span className="text-sm font-medium text-purple-600 truncate">
												{stack.title}
											</span>
										</p>
									</div>
								</div>
								{userEnsemblesCount > 1 && (
									<EnsembleBadges
										ensembles={stack.ensembles}
										className="mt-2 flex gap-1 flex-wrap"
										badgeClassName="truncate"
									/>
								)}
							</div>
						</TableMobileSelectableLink>
					</TableMobileListItem>
				))}
			</TableMobile>
		</div>
	);
};

export default RiserStackTableMobile;
