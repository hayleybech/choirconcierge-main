import React, { useState } from 'react';
import { Link, useForm } from '@inertiajs/react';
import TenantLayout from '../../../Layouts/TenantLayout';
import AppHead from '../../../components/AppHead';
import Prose from '../../../components/Prose';
import Button from '../../../components/inputs/ButtonV2';
import Icon from '../../../components/Icon';
import useRoute from '../../../hooks/useRoute';
import ForumPageLayout from '../ForumPageLayout';
import ChannelBadge, { ChannelName } from '../ChannelBadge';
import formatRelativeDate from '../formatRelativeDate';
import MemberLink from '../MemberLink';
import TopicFilter from './TopicFilter';
import TopicList from './TopicList';

const Index = ({ category, topicCategories = [], topics = [], canCreateTopic, filter = 'all', setSidebarOpen }) => {
	const { route } = useRoute();
	const [isCreating, setIsCreating] = useState(false);
	const latestPinnedTopic = topics.find(topic => topic.is_pinned);
	const allFilterHref = category
		? route('forum.categories.topics.index', { forum_category: category.id })
		: route('forum.topics.index');
	const unreadFilterHref = category
		? route('forum.categories.topics.index', { forum_category: category.id, filter: 'unread' })
		: route('forum.topics.index', { filter: 'unread' });
	const actions =
		canCreateTopic && !isCreating
			? [{ label: 'Start a thread', icon: 'plus', onClick: () => setIsCreating(true) }]
			: [];
	const latestPinnedChannelIndex = topicCategories.findIndex(
		topicCategory => String(topicCategory.id) === String(latestPinnedTopic?.category_id)
	);

	return (
		<>
			<AppHead title={category ? `# ${category.title}` : 'Threads'} />
			<ForumPageLayout
				title={category ? <ChannelName name={category.title} /> : 'Threads'}
				description={
					category ? 'Threads in this channel.' : 'Browse the latest threads from channels you can access.'
				}
				breadcrumbs={
					category
						? [
								{
									name: `# ${category.title}`,
									url: route('forum.categories.topics.index', { forum_category: category.id }),
								},
						  ]
						: []
				}
				actions={actions}
				setSidebarOpen={setSidebarOpen}
			>
				<div className="grid grid-cols-1 items-start gap-6 lg:grid-cols-[minmax(0,1fr)_20rem]">
					<div className="min-w-0 space-y-5">
						{isCreating && (
							<TopicForm
								key={category?.id ?? 'latest'}
								categories={topicCategories}
								categoryId={category?.id}
								onCancel={() => setIsCreating(false)}
							/>
						)}
						<TopicFilter filter={filter} allHref={allFilterHref} unreadHref={unreadFilterHref} />
						<TopicList
							topics={topics}
							channels={topicCategories}
							showTable
							emptyTitle={
								filter === 'unread'
									? 'No unread threads'
									: category
									? 'No threads in this channel yet'
									: 'No threads yet'
							}
							emptyDescription={
								filter === 'unread'
									? 'You are all caught up.'
									: category
									? 'Start the first thread in this channel.'
									: 'Start a thread and invite your choir to join in.'
							}
						/>
					</div>

					<aside className="space-y-5">
						<section
							aria-label="Latest pinned thread"
							className={`rounded-lg border p-5 shadow-sm ${
								latestPinnedTopic ? 'border-purple-300 bg-purple-50' : 'border-gray-200 bg-white'
							}`}
						>
							{latestPinnedTopic ? (
								<div className="space-y-2">
									<div className="flex flex-wrap items-center gap-1.5 text-xs text-gray-500">
										{latestPinnedTopic.author_avatar_url && (
											<img
												src={latestPinnedTopic.author_avatar_url}
												alt=""
												className="h-6 w-6 rounded-full object-cover"
											/>
										)}
										<MemberLink
											membershipId={latestPinnedTopic.author_membership_id}
											className="font-medium"
										>
											{latestPinnedTopic.author_name}
										</MemberLink>
										{latestPinnedTopic.category_title && (
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
														forum_category: latestPinnedTopic.category_id,
													})}
												>
													<ChannelBadge
														name={latestPinnedTopic.category_title}
														colourIndex={latestPinnedChannelIndex}
													/>
												</Link>
											</>
										)}
									</div>
									<Link
										href={route('forum.topics.show', { forum_topic: latestPinnedTopic.id })}
										className={`flex items-start gap-2 ${
											latestPinnedTopic.is_unread ? 'font-semibold' : 'font-normal'
										} text-purple-700 hover:text-purple-900`}
									>
										<Icon icon="thumbtack" className="mt-0.5 shrink-0 text-purple-700" />
										<span>{latestPinnedTopic.title}</span>
									</Link>
									{latestPinnedTopic.body_html && (
										<Prose
											content={latestPinnedTopic.body_html}
											className="line-clamp-3 text-sm text-gray-600"
										/>
									)}
									<time dateTime={latestPinnedTopic.created_at} className="text-xs text-gray-500">
										{formatRelativeDate(latestPinnedTopic.created_at)}
									</time>
								</div>
							) : (
								<p className="text-sm text-gray-500">No pinned threads yet.</p>
							)}
						</section>

						<section className="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
							<h2 className="mb-3 text-base font-semibold text-gray-900">Channels</h2>
							{topicCategories.length > 0 ? (
								<ul className="space-y-2">
									{topicCategories.map((topicCategory, channelIndex) => (
										<li key={topicCategory.id}>
											<Link
												href={route('forum.categories.topics.index', {
													forum_category: topicCategory.id,
												})}
												aria-current={category?.id === topicCategory.id ? 'page' : undefined}
												className={`flex items-center justify-between gap-2 rounded px-2 py-2 ${
													category?.id === topicCategory.id
														? 'bg-purple-50 ring-2 ring-purple-300 ring-offset-1'
														: ''
												}`}
											>
												<ChannelBadge name={topicCategory.title} colourIndex={channelIndex} />
												<span className="shrink-0 text-xs text-gray-500">
													{topicCategory.topics_count ?? 0}{' '}
													{topicCategory.topics_count === 1 ? 'thread' : 'threads'}
												</span>
											</Link>
										</li>
									))}
								</ul>
							) : (
								<p className="text-sm text-gray-500">No channels available.</p>
							)}
						</section>
					</aside>
				</div>
			</ForumPageLayout>
		</>
	);
};

const TopicForm = ({ categories, categoryId, onCancel }) => {
	const { route } = useRoute();
	const { data, setData, post, processing, errors } = useForm({
		title: '',
		body: '',
		selectedCategoryId: categoryId ?? categories[0]?.id ?? '',
	});

	const submit = event => {
		event.preventDefault();
		post(route('forum.categories.topics.store', { forum_category: categoryId ?? data.selectedCategoryId }), {
			preserveScroll: true,
			onSuccess: onCancel,
		});
	};

	return (
		<form onSubmit={submit} className="space-y-5 rounded-lg border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
			<h2 className="text-lg font-semibold text-gray-900">Start a thread</h2>
			{!categoryId && categories.length > 1 && (
				<div>
					<label htmlFor="thread-channel" className="mb-1 block text-sm font-medium text-gray-700">
						Channel
					</label>
					<select
						id="thread-channel"
						value={data.selectedCategoryId}
						onChange={event => setData('selectedCategoryId', event.target.value)}
						className="block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
					>
						{categories.map(topicCategory => (
							<option
								key={topicCategory.id}
								value={topicCategory.id}
							>{`# ${topicCategory.title}`}</option>
						))}
					</select>
				</div>
			)}
			<div>
				<label htmlFor="thread-title" className="mb-1 block text-sm font-medium text-gray-700">
					Title
				</label>
				<input
					id="thread-title"
					value={data.title}
					onChange={event => setData('title', event.target.value)}
					className="block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
					maxLength={255}
					required
				/>
				{errors.title && <p className="mt-1 text-sm text-red-700">{errors.title}</p>}
			</div>
			<div>
				<label htmlFor="thread-body" className="mb-1 block text-sm font-medium text-gray-700">
					Message (Markdown supported)
				</label>
				<textarea
					id="thread-body"
					value={data.body}
					onChange={event => setData('body', event.target.value)}
					className="block min-h-40 w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
					maxLength={20000}
					required
				/>
				{errors.body && <p className="mt-1 text-sm text-red-700">{errors.body}</p>}
			</div>
			<div className="flex justify-end gap-2">
				<Button weight="clear" onClick={onCancel}>
					Cancel
				</Button>
				<Button type="submit" intent="primary" disabled={processing}>
					{processing ? 'Posting…' : 'Post thread'}
				</Button>
			</div>
		</form>
	);
};

Index.layout = page => <TenantLayout children={page} />;

export default Index;
