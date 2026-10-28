import React, { useState } from 'react';
import { Link, useForm } from '@inertiajs/react';
import TenantLayout from '../../../Layouts/TenantLayout';
import AppHead from '../../../components/AppHead';
import Badge from '../../../components/Badge';
import EmptyState from '../../../components/EmptyState';
import Icon from '../../../components/Icon';
import Button from '../../../components/inputs/ButtonV2';
import Prose from '../../../components/Prose';
import useRoute from '../../../hooks/useRoute';
import ForumPageLayout from '../ForumPageLayout';

const Index = ({ category, topics, canCreateTopic, setSidebarOpen }) => {
	const { route } = useRoute();
	const [isCreating, setIsCreating] = useState(false);
	const actions = canCreateTopic && !isCreating
		? [{ label: 'Start a discussion', icon: 'plus', onClick: () => setIsCreating(true) }]
		: [];

	return (
		<>
			<AppHead title={category.title} />
			<ForumPageLayout
				title={category.title}
				description="Join the conversation or start a new discussion."
				breadcrumbs={[
					{
						name: category.title,
						url: route('forum.categories.topics.index', { forum_category: category.id }),
					},
				]}
				actions={actions}
				setSidebarOpen={setSidebarOpen}
			>
				<div className="mx-auto max-w-5xl space-y-5">
					{isCreating && <TopicForm categoryId={category.id} onCancel={() => setIsCreating(false)} />}

					{topics.length > 0 ? (
						<div className="space-y-3">
							{topics.map(topic => (
								<article
									key={topic.id}
									className="flex flex-col gap-4 rounded-lg border border-gray-200 bg-white p-5 shadow-sm transition-colors hover:border-purple-300 sm:flex-row sm:items-center sm:justify-between sm:p-6"
								>
									<div className="min-w-0 flex-1">
										<div className="flex flex-wrap items-center gap-2">
											<Link
												href={route('forum.topics.show', { forum_topic: topic.id })}
												className="text-lg font-semibold text-gray-900 hover:text-purple-700"
											>
												{topic.title}
											</Link>
											{topic.is_pinned && <Badge colour="bg-purple-100 text-purple-800">Pinned</Badge>}
										</div>
										<Prose content={topic.body_html} className="mt-3 text-sm text-gray-700" />
										<p className="mt-4 text-xs text-gray-500">
											Started by {topic.author_name} <span aria-hidden="true">·</span>{' '}
											{new Date(topic.created_at).toLocaleString()}
										</p>
									</div>
									<div className="flex items-center gap-2 border-t border-gray-100 pt-3 text-sm text-gray-600 sm:flex-col sm:border-l sm:border-t-0 sm:pl-5 sm:pt-0">
										<Icon icon="comments" className="text-purple-600" />
										<span className="font-semibold text-gray-900">{topic.replies_count}</span>
										<span>{topic.replies_count === 1 ? 'reply' : 'replies'}</span>
									</div>
								</article>
							))}
						</div>
					) : (
						<div className="rounded-lg border border-gray-200 bg-white shadow-sm">
							<EmptyState
								title="No discussions yet"
								description="Start a conversation and invite your choir to join in."
								icon="comments"
							/>
						</div>
					)}
				</div>
			</ForumPageLayout>
		</>
	);
};

const TopicForm = ({ categoryId, onCancel }) => {
	const { route } = useRoute();
	const { data, setData, post, processing, errors } = useForm({ title: '', body: '' });

	const submit = event => {
		event.preventDefault();
		post(route('forum.categories.topics.store', { forum_category: categoryId }), {
			preserveScroll: true,
			onSuccess: onCancel,
		});
	};

	return (
		<form onSubmit={submit} className="space-y-5 rounded-lg border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
			<h2 className="text-lg font-semibold text-gray-900">Start a discussion</h2>
			<div>
				<label htmlFor="topic-title" className="mb-1 block text-sm font-medium text-gray-700">
					Title
				</label>
				<input
					id="topic-title"
					value={data.title}
					onChange={event => setData('title', event.target.value)}
					className="block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
					maxLength={255}
					required
				/>
				{errors.title && <p className="mt-1 text-sm text-red-700">{errors.title}</p>}
			</div>
			<div>
				<label htmlFor="topic-body" className="mb-1 block text-sm font-medium text-gray-700">
					Message (Markdown supported)
				</label>
				<textarea
					id="topic-body"
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
					{processing ? 'Posting…' : 'Post discussion'}
				</Button>
			</div>
		</form>
	);
};

Index.layout = page => <TenantLayout children={page} />;

export default Index;
