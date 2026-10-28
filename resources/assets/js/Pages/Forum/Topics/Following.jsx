import React from 'react';
import TenantLayout from '../../../Layouts/TenantLayout';
import AppHead from '../../../components/AppHead';
import useRoute from '../../../hooks/useRoute';
import ForumPageLayout from '../ForumPageLayout';
import TopicList from './TopicList';

const Following = ({ topics, setSidebarOpen }) => {
	const { route } = useRoute();
	return (
		<>
			<AppHead title="Followed Topics" />
			<ForumPageLayout
				title="Followed Topics"
				description="Topics you follow, all in one place."
				activeTab="following"
				breadcrumbs={[{ name: 'Followed Topics', url: route('forum.following.index') }]}
				setSidebarOpen={setSidebarOpen}
			>
				<div className="mx-auto max-w-5xl">
					<TopicList
						topics={topics}
						emptyTitle="No followed topics"
						emptyDescription="Follow a topic to keep it close at hand."
					/>
				</div>
			</ForumPageLayout>
		</>
	);
};

Following.layout = page => <TenantLayout children={page} />;

export default Following;
