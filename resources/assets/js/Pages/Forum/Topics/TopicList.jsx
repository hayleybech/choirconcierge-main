import React from 'react';
import { Link } from '@inertiajs/react';
import Badge from '../../../components/Badge';
import EmptyState from '../../../components/EmptyState';
import Icon from '../../../components/Icon';
import Prose from '../../../components/Prose';
import useRoute from '../../../hooks/useRoute';
import ReactionSummary from './ReactionSummary';

const TopicList = ({
	topics = [],
	showCategories = true,
	emptyTitle = 'No topics yet',
	emptyDescription = 'Start a topic and invite your choir to join in.',
}) => {
	const { route } = useRoute();

	if (topics.length === 0) {
		return (
			<div className="rounded-lg border border-gray-200 bg-white shadow-sm">
				<EmptyState title={emptyTitle} description={emptyDescription} icon="comments" />
			</div>
		);
	}

	return (
		<div className="space-y-3">
			{topics.map(topic => (
				<article key={topic.id} className="rounded-lg border border-gray-200 bg-white p-4 shadow-sm sm:p-5">
					<div className="flex items-start gap-3">
						{topic.author_avatar_url && (
							<img src={topic.author_avatar_url} alt="" className="h-10 w-10 shrink-0 rounded-full object-cover" />
						)}
						<div className="min-w-0 flex-1">
							<div className="flex flex-wrap items-center gap-2">
								{showCategories && topic.category_id && (
									<Link href={route('forum.categories.topics.index', { forum_category: topic.category_id })}>
										<Badge colour="bg-purple-100 text-purple-800">{topic.category_title}</Badge>
									</Link>
								)}
								{topic.is_pinned && <Badge colour="bg-purple-100 text-purple-800">Pinned</Badge>}
							</div>
							<Link
								href={route('forum.topics.show', { forum_topic: topic.id })}
								className="mt-2 block text-lg font-semibold text-gray-900 hover:text-purple-700"
							>
								{topic.title}
							</Link>
							{topic.body_html && <Prose content={topic.body_html} className="mt-2 line-clamp-3 text-sm text-gray-700" />}
							<div className="mt-4 flex flex-wrap items-center justify-between gap-3">
								<p className="text-xs text-gray-500">
									{topic.author_name} <span aria-hidden="true">·</span> {new Date(topic.created_at).toLocaleString()}
								</p>
								<div className="flex flex-wrap items-center gap-3">
									<span className="inline-flex items-center gap-1 text-sm text-gray-600">
										<Icon icon="comments" className="text-purple-600" />
										<span>{topic.replies_count ?? 0} {topic.replies_count === 1 ? 'reply' : 'replies'}</span>
									</span>
									<ReactionSummary reactions={topic.reactions} />
								</div>
							</div>
						</div>
					</div>
				</article>
			))}
		</div>
	);
};

export default TopicList;