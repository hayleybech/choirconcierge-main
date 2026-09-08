import React from 'react';
import PageHeader from "../../../components/PageHeader/PageHeader";
import TenantLayout from "../../../Layouts/TenantLayout";
import AppHead from "../../../components/AppHead";
import useRoute from "../../../hooks/useRoute";
import useBulkEdit from "../../../hooks/useBulkEdit";
import BulkEditBar from "../../../components/BulkEditBar";
import Button from "../../../components/inputs/Button";
import Icon from "../../../components/Icon";
import LearningStatusTable from "./LearningStatusTable";
import LearningStatusTableMobile from "./LearningStatusTableMobile";
import IndexContainer from "../../../components/IndexContainer";
import { router } from '@inertiajs/react';

const Index = ({ song, voiceParts }) => {
	const { route } = useRoute();
	const singers = voiceParts.flatMap(voicePart =>
		voicePart.members.map(singer => ({ ...singer, voicePart }))
	);
	const bulkEdit = useBulkEdit(singers, true, false, 'Singer', true);

	const markSelectedAsPerformanceReady = () => {
		router.post(
			route('songs.singers.bulk-update', { song }),
		{ singer_ids: bulkEdit.selectedIds },
		{ preserveScroll: true, onSuccess: () => bulkEdit.clearSelections() }
		);
	};

	return (
		<>
			<AppHead title={`Learning Status List - ${song.title}`} />
			<PageHeader
				title="Learning Status List"
				icon="fa-list-music"
				breadcrumbs={[
					{ name: 'Dashboard', url: route('dash') },
					{ name: 'Songs', url: route('songs.index') },
					{ name: song.title, url: route('songs.show', { song }) },
					{ name: 'Learning Status List', url: route('songs.singers.index', { song }) },
				]}
				actions={[bulkEdit.action].filter(Boolean)}
			/>

			<BulkEditBar
				bulkEdit={bulkEdit}
				actions={
					<Button size="xs" variant="clear-inverse" onClick={markSelectedAsPerformanceReady}>
						<Icon icon="check-double" className="text-emerald-500" />
						Mark as Performance Ready
					</Button>
				}
			/>

			<IndexContainer
				tableDesktop={<LearningStatusTable song={song} singers={singers} bulkEdit={bulkEdit} />}
				tableMobile={<LearningStatusTableMobile song={song} singers={singers} bulkEdit={bulkEdit} />}
			/>
		</>
	);
};

Index.layout = page => <TenantLayout children={page} />;

export default Index;
