import React from 'react';
import { Link } from '@inertiajs/react';
import TenantLayout from '../../../Layouts/TenantLayout';
import AppHead from '../../../components/AppHead';
import Badge from '../../../components/Badge';
import EmptyState from '../../../components/EmptyState';
import Icon from '../../../components/Icon';
import useRoute from '../../../hooks/useRoute';
import ForumPageLayout from '../ForumPageLayout';

const Following = ({ topics, setSidebarOpen }) => {
	const { route } = useRoute();

	return (
		<>
			<AppHead title="Followed discussions" />
			<ForumPageLayout
				title="Followed discussions"
				description="Pick up where you left off in conversations you follow."
				activeTab="following"
				breadcrumbs={[{ name: 'Followed discussions', url: route('forum.following.index') }]}
				setSidebarOpen={setSidebarOpen}
			>
				<div className="mx-auto max-w-5xl">
					{topics.length > 0 ? (
						<div className="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
							{topics.map(topic => (
								<article key={topic.id} className="border-b border-gray-200 p-5 last:border-b-0 sm:p-6">
									<div className="flex flex-wrap items-center gap-2">
										<Badge colour="bg-purple-100 text-purple-800">{topic.category_title}</Badge>
										<Icon icon="bookmark" className="text-purple-600" />
									</div>
									<Link
										href={route('forum.topics.show', { forum_topic: topic.id })}
										className="mt-3 block text-lg font-semibold text-gray-900 hover:text-purple-700"
									>
										{topic.title}
									</Link>
									<p className="mt-2 text-sm text-gray-500">
										{topic.author_name} <span aria-hidden="true">·</span>{' '}
										{new Date(topic.created_at).toLocaleString()}
									</p>
								</article>
							))}
						</div>
					) : (
						<div className="rounded-lg border border-gray-200 bg-white shadow-sm">
							<EmptyState
								title="No followed discussions"
								description="Follow a discussion to keep it close at hand."
								icon="bookmark"
							/>
						</div>
					)}
				</div>
			</ForumPageLayout>
		</>
	);
};

Following.layout = page => <TenantLayout children={page} />;

export default Following;
