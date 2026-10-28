import React, { useState } from 'react';
import { Link, useForm } from '@inertiajs/react';
import TenantLayout from '../../../Layouts/TenantLayout';
import AppHead from '../../../components/AppHead';
import Button from '../../../components/inputs/ButtonV2';
import Icon from '../../../components/Icon';
import useRoute from '../../../hooks/useRoute';
import ForumPageLayout from '../ForumPageLayout';
import TopicList from './TopicList';

const Index = ({ category, topicCategories = [], topics = [], canCreateTopic, setSidebarOpen }) => {
	const { route } = useRoute();
	const [isCreating, setIsCreating] = useState(false);
	const latestPinnedTopic = topics.find(topic => topic.is_pinned);
	const actions = canCreateTopic && !isCreating
		? [{ label: 'Start a topic', icon: 'plus', onClick: () => setIsCreating(true) }]
		: [];

	return (
		<>
			<AppHead title={category?.title ?? 'Discussions'} />
			<ForumPageLayout
				title={category?.title ?? 'Discussions'}
				description={category ? 'Topics in this category.' : 'Browse the latest topics from categories you can access.'}
				breadcrumbs={category ? [{
					name: category.title,
					url: route('forum.categories.topics.index', { forum_category: category.id }),
				}] : []}
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
						<TopicList
							topics={topics}
							showTable
							emptyTitle={category ? 'No topics in this category yet' : 'No topics yet'}
							emptyDescription={category
								? 'Start the first topic in this category.'
								: 'Start a topic and invite your choir to join in.'}
						/>
					</div>

					<aside className="space-y-5">
						<section className="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
							<h2 className="mb-4 flex items-center gap-2 text-base font-semibold text-gray-900">
								<Icon icon="thumbtack" className="text-purple-700" />
								Latest pinned topic
							</h2>
							{latestPinnedTopic ? (
								<div className="space-y-2">
									<Link
										href={route('forum.topics.show', { forum_topic: latestPinnedTopic.id })}
										className="block font-semibold text-gray-900 hover:text-purple-700"
									>
										{latestPinnedTopic.title}
									</Link>
									{latestPinnedTopic.category_title && (
										<Link
											href={route('forum.categories.topics.index', { forum_category: latestPinnedTopic.category_id })}
											className="block text-sm text-purple-800 hover:text-purple-600"
										>
											{latestPinnedTopic.category_title}
										</Link>
									)}
									<p className="text-xs text-gray-500">
										{latestPinnedTopic.author_name} <span aria-hidden="true">·</span>{' '}
										{new Date(latestPinnedTopic.created_at).toLocaleString()}
									</p>
								</div>
							) : (
								<p className="text-sm text-gray-500">No pinned topics yet.</p>
							)}
						</section>

						<section className="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
							<h2 className="mb-3 text-base font-semibold text-gray-900">Categories</h2>
							{topicCategories.length > 0 ? (
								<ul className="divide-y divide-gray-100">
									{topicCategories.map(topicCategory => (
										<li key={topicCategory.id}>
											<Link
												href={route('forum.categories.topics.index', { forum_category: topicCategory.id })}
												aria-current={category?.id === topicCategory.id ? 'page' : undefined}
												className={`block py-2 text-sm ${category?.id === topicCategory.id
													? 'font-semibold text-purple-800'
													: 'text-gray-700 hover:text-purple-700'}`}
											>
												{topicCategory.title}
											</Link>
										</li>
									))}
								</ul>
							) : (
								<p className="text-sm text-gray-500">No categories available.</p>
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
			<h2 className="text-lg font-semibold text-gray-900">Start a topic</h2>
			{!categoryId && categories.length > 1 && (
				<div>
					<label htmlFor="topic-category" className="mb-1 block text-sm font-medium text-gray-700">
						Category
					</label>
					<select
						id="topic-category"
						value={data.selectedCategoryId}
						onChange={event => setData('selectedCategoryId', event.target.value)}
						className="block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
					>
						{categories.map(topicCategory => (
							<option key={topicCategory.id} value={topicCategory.id}>{topicCategory.title}</option>
						))}
					</select>
				</div>
			)}
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
					{processing ? 'Posting…' : 'Post topic'}
				</Button>
			</div>
		</form>
	);
};

Index.layout = page => <TenantLayout children={page} />;

export default Index;
