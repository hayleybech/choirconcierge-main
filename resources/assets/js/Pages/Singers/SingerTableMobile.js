import React from 'react';
import SingerCategoryTag from "../../components/SingerCategoryTag";
import VoicePartTag from '../../components/VoicePartTag';
import TableMobile, {
	TableMobileListItem,
	TableMobileSelect,
	TableMobileSelectableLink,
} from '../../components/TableMobile';
import Icon from "../../components/Icon";
import SingerStatus from "../../SingerStatus";
import useRoute from "../../hooks/useRoute";
import Pagination from '../../components/Pagination';
import BulkEditBarMobile from '../../components/BulkEditBarMobile';

const SingerTableMobile = ({ singers, pagination, bulkEdit }) => {
    const { route } = useRoute();

    return (
		<div>
			<BulkEditBarMobile totalItems={singers.length} bulkEdit={bulkEdit} />
			<TableMobile pagination={<Pagination details={pagination} />}>
				{singers.map((singer) => (
					<TableMobileListItem key={singer.id}>
						<TableMobileSelect bulkEdit={bulkEdit} value={singer.id} />
						<TableMobileSelectableLink
							bulkEdit={bulkEdit}
							value={singer.id}
							url={route('singers.show', {singer: singer.id})}
						>
							<div className="shrink-0">
								<img className="h-12 w-12 rounded-lg" src={singer.user.avatar_url} alt={singer.user.name} />
							</div>
							<div className="min-w-0 flex-1 px-4 lg:grid lg:grid-cols-2 lg:gap-4">
								<div>
									<div className="flex items-center justify-between">
										<p className="flex items-center min-w-0 mr-1.5">
											<SingerCategoryTag status={new SingerStatus(singer.category.slug)} />
											<span className="text-sm font-medium text-purple-600 truncate">{singer.user.name}</span>
										</p>
										{singer.enrolments.length < 3 && singer.enrolments.map((enrolment) => (
											enrolment.voice_part && <VoicePartTag key={enrolment.id} title={enrolment.voice_part.title} colour={enrolment.voice_part.colour} />
										))}
									</div>
									<div className="flex items-center justify-between">
										<p className="mt-2 flex items-center text-sm text-gray-500 min-w-0">
											<Icon icon="phone" mr className="text-gray-400" />
											<span className="truncate">{singer.user.phone ?? 'No phone'}</span>
										</p>

										<p className="mt-2 hidden sm:flex items-center text-sm text-gray-500 min-w-0">
											<Icon icon="envelope" mr className="text-gray-400" />
											<span className="truncate">{singer.user.email}</span>
										</p>
									</div>
								</div>
							</div>
						</TableMobileSelectableLink>
					</TableMobileListItem>
				))}
			</TableMobile>
		</div>
    );
}

export default SingerTableMobile;