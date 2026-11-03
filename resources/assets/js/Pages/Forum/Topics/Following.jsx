import React from 'react';
import TenantLayout from '../../../Layouts/TenantLayout';
import AppHead from '../../../components/AppHead';
import useRoute from '../../../hooks/useRoute';
import ForumPageLayout from '../ForumPageLayout';
import TopicFilter from './TopicFilter';
import TopicList from './TopicList';

const Following = ({ topics, filter = 'all', setSidebarOpen }) => {
	const { route } = useRoute();
	const allFilterHref = route('forum.following.index');
	const unreadFilterHref = route('forum.following.index', { filter: 'unread' });
	return (
		<>
			<AppHead title="Followed Threads" />
			<ForumPageLayout
				title="Followed Threads"
				description="Threads you follow, all in one place."
				activeTab="following"
				breadcrumbs={[{ name: 'Followed Threads', url: route('forum.following.index') }]}
				setSidebarOpen={setSidebarOpen}
			>
				<div className="mx-auto max-w-5xl">
					<TopicFilter filter={filter} allHref={allFilterHref} unreadHref={unreadFilterHref} />
					<TopicList
						topics={topics}
						emptyTitle={filter === 'unread' ? 'No unread threads' : 'No followed threads'}
						emptyDescription={
							filter === 'unread' ? 'You are all caught up.' : 'Follow a thread to keep it close at hand.'
						}
					/>
				</div>
			</ForumPageLayout>
		</>
	);
};

Following.layout = page => <TenantLayout children={page} />;

export default Following;
