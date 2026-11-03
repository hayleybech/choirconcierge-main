import React from 'react';
import { Link } from '@inertiajs/react';
import EmptyState from '../../../components/EmptyState';
import Icon from '../../../components/Icon';
import Prose from '../../../components/Prose';
import Table, { TableCell, TableHeading, TBody, THead } from '../../../components/Table';
import useRoute from '../../../hooks/useRoute';
import ChannelBadge from '../ChannelBadge';
import formatRelativeDate from '../formatRelativeDate';
import MemberLink from '../MemberLink';
import ReactionSummary from './ReactionSummary';

const TopicList = ({
	topics = [],
	channels = [],
	showCategories = true,
	showTable = false,
	emptyTitle = 'No threads yet',
	emptyDescription = 'Start a thread and invite your choir to join in.',
}) => {
	const { route } = useRoute();
	const getChannelIndex = channelId => {
		const channelIndex = channels.findIndex(channel => String(channel.id) === String(channelId));

		return channelIndex >= 0
			? channelIndex
			: Math.max(
					0,
					topics.findIndex(topic => String(topic.category_id) === String(channelId))
			  );
	};

	if (topics.length === 0) {
		return (
			<div className="rounded-lg border border-gray-200 bg-white shadow-sm">
				<EmptyState title={emptyTitle} description={emptyDescription} icon="comments" />
			</div>
		);
	}

	if (showTable) {
		return (
			<Table>
				<THead>
					<tr>
						<TableHeading>Thread</TableHeading>
						<TableHeading>Replies</TableHeading>
						<TableHeading>Created</TableHeading>
						<TableHeading>Activity</TableHeading>
					</tr>
				</THead>
				<TBody>
					{topics.map(topic => (
						<tr key={topic.id} className={topic.is_unread ? undefined : 'bg-gray-100'}>
							<TableCell className="whitespace-normal">
								<div className="flex min-w-56 items-center gap-3 -ml-4">
									<span className="w-5 shrink-0 text-center">
										{topic.is_pinned && (
											<span
												role="img"
												aria-label="Pinned thread"
												title="Pinned thread"
												className="inline-block text-base text-purple-600"
											>
												<Icon icon="thumbtack" />
											</span>
										)}
									</span>
									<div className="min-w-0 flex-1">
										<div className="flex flex-wrap items-center gap-1.5 text-xs text-gray-500">
											{topic.author_avatar_url && (
												<img
													src={topic.author_avatar_url}
													alt=""
													className="h-6 w-6 rounded-full object-cover"
												/>
											)}
											<MemberLink
												membershipId={topic.author_membership_id}
												className="font-medium"
											>
												{topic.author_name}
											</MemberLink>
											{showCategories && topic.category_id && topic.category_title && (
												<>
													<span aria-hidden="true">
														<Icon
															icon="chevron-right"
															size="text-[10px]"
															className="text-gray-400"
														/>
													</span>
													<Link
														href={route('forum.categories.topics.index', {
															forum_category: topic.category_id,
														})}
													>
														<ChannelBadge
															name={topic.category_title}
															colourIndex={getChannelIndex(topic.category_id)}
														/>
													</Link>
												</>
											)}
										</div>
										<div className="mt-1 flex min-w-0 items-center gap-2">
											{topic.is_unread && (
												<span
													role="img"
													aria-label="Unread thread"
													title="Unread thread"
													className="h-2 w-2 shrink-0 rounded-full bg-purple-600"
												/>
											)}
											<Link
												href={route('forum.topics.show', { forum_topic: topic.id })}
												className={`min-w-0 ${
													topic.is_unread ? 'font-semibold' : 'font-normal'
												} text-purple-700 hover:text-purple-900`}
											>
												{topic.title}
											</Link>
										</div>
										{topic.body_html && (
											<Prose
												content={topic.body_html}
												className="mt-1 line-clamp-1 text-xs text-gray-600"
											/>
										)}
										<div className="mt-2">
											<ReactionSummary reactions={topic.reactions} compact />
										</div>
									</div>
								</div>
							</TableCell>
							<TableCell>
								{topic.replies_count ?? 0} {topic.replies_count === 1 ? 'reply' : 'replies'}
							</TableCell>
							<TableCell>
								<time dateTime={topic.created_at}>{formatRelativeDate(topic.created_at)}</time>
							</TableCell>
							<TableCell className="whitespace-normal">
								{topic.latest_reply ? (
									<div className="flex items-center gap-2">
										{topic.latest_reply.author_avatar_url && (
											<img
												src={topic.latest_reply.author_avatar_url}
												alt=""
												className="h-6 w-6 shrink-0 rounded-full object-cover"
											/>
										)}
										<div>
											<MemberLink
												membershipId={topic.latest_reply.author_membership_id}
												className="block font-medium"
											>
												{topic.latest_reply.author_name}
											</MemberLink>
											<time
												className="text-xs text-gray-500"
												dateTime={topic.latest_reply.created_at}
											>
												{formatRelativeDate(topic.latest_reply.created_at)}
											</time>
										</div>
									</div>
								) : (
									<span className="text-gray-400">No replies yet</span>
								)}
							</TableCell>
						</tr>
					))}
				</TBody>
			</Table>
		);
	}

	return (
		<div className="space-y-3">
			{topics.map(topic => (
				<article
					key={topic.id}
					className={`rounded-lg border border-gray-200 p-4 shadow-sm sm:p-5 ${
						topic.is_unread ? 'bg-white' : 'bg-gray-100'
					}`}
				>
					<div className="flex items-center gap-3">
						<span className="w-5 shrink-0 text-center">
							{topic.is_pinned && (
								<span
									role="img"
									aria-label="Pinned thread"
									title="Pinned thread"
									className="inline-block text-lg text-purple-600"
								>
									<Icon icon="thumbtack" />
								</span>
							)}
						</span>
						<div className="min-w-0 flex-1">
							<div className="flex flex-wrap items-center gap-1.5 text-xs text-gray-500">
								{topic.author_avatar_url && (
									<img
										src={topic.author_avatar_url}
										alt=""
										className="h-6 w-6 rounded-full object-cover"
									/>
								)}
								<MemberLink membershipId={topic.author_membership_id} className="font-medium">
									{topic.author_name}
								</MemberLink>
								{showCategories && topic.category_id && topic.category_title && (
									<>
										<span aria-hidden="true">
											<Icon icon="chevron-right" size="text-[10px]" className="text-gray-400" />
										</span>
										<Link
											href={route('forum.categories.topics.index', {
												forum_category: topic.category_id,
											})}
										>
											<ChannelBadge
												name={topic.category_title}
												colourIndex={getChannelIndex(topic.category_id)}
											/>
										</Link>
									</>
								)}
							</div>
							<div className="mt-1 flex items-center gap-2">
								{topic.is_unread && (
									<span
										role="img"
										aria-label="Unread thread"
										title="Unread thread"
										className="h-2 w-2 shrink-0 rounded-full bg-purple-600"
									/>
								)}
								<Link
									href={route('forum.topics.show', { forum_topic: topic.id })}
									className={`text-lg ${
										topic.is_unread ? 'font-semibold' : 'font-normal'
									} text-purple-700 hover:text-purple-900`}
								>
									{topic.title}
								</Link>
							</div>
							{topic.body_html && (
								<Prose content={topic.body_html} className="mt-2 line-clamp-3 text-sm text-gray-700" />
							)}
							<div className="mt-4 flex flex-wrap items-center justify-between gap-3">
								<time dateTime={topic.created_at} className="text-xs text-gray-500">
									{formatRelativeDate(topic.created_at)}
								</time>
								<div className="flex flex-wrap items-center gap-3">
									<span className="inline-flex items-center gap-1 text-sm text-gray-600">
										<Icon icon="comments" className="text-purple-600" />
										<span>
											{topic.replies_count ?? 0} {topic.replies_count === 1 ? 'reply' : 'replies'}
										</span>
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
