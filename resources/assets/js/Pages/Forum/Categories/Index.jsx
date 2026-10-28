import React, { useState } from 'react';
import { router, useForm } from '@inertiajs/react';
import TenantLayout from '../../../Layouts/TenantLayout';
import AppHead from '../../../components/AppHead';
import Badge from '../../../components/Badge';
import EmptyState from '../../../components/EmptyState';
import Icon from '../../../components/Icon';
import Button from '../../../components/inputs/ButtonV2';
import useRoute from '../../../hooks/useRoute';
import ForumPageLayout from '../ForumPageLayout';
import TopicList from '../Topics/TopicList';

const Index = ({ categories, ensembles, canManageCategories, setSidebarOpen }) => {
	const { route } = useRoute();
	const [activeForm, setActiveForm] = useState(null);
	const formCategory = activeForm === 'new' ? null : activeForm;
	const actions = canManageCategories && !activeForm
		? [{ label: 'New category', icon: 'plus', onClick: () => setActiveForm('new') }]
		: [];

	const deleteCategory = category => {
		if (window.confirm(`Delete the "${category.title}" category?`)) {
			router.delete(route('forum.categories.destroy', { forum_category: category.id }), {
				preserveScroll: true,
			});
		}
	};

	return (
		<>
			<AppHead title="Categories" />
			<ForumPageLayout
				title="Categories"
				description="Browse topics grouped by category."
				activeTab="categories"
				actions={actions}
				setSidebarOpen={setSidebarOpen}
			>
				<div className="mx-auto max-w-5xl space-y-5">
					{activeForm && (
						<CategoryForm
							key={formCategory?.id ?? 'new'}
							category={formCategory}
							ensembles={ensembles}
							onCancel={() => setActiveForm(null)}
						/>
					)}

					<section className="space-y-4">
						<div className="flex items-end justify-between gap-3">
							<div>
								<h2 className="text-lg font-semibold text-gray-900">Topics by category</h2>
								<p className="mt-1 text-sm text-gray-500">
									Topics are grouped by the category they belong to.
								</p>
							</div>
							<span className="text-sm text-gray-500">
								{categories.length} {categories.length === 1 ? 'category' : 'categories'}
							</span>
						</div>

						{categories.length > 0 ? (
							<div className="space-y-4">
								{categories.map(category => (
									<article
										key={category.id}
										className="rounded-lg border border-gray-200 bg-white p-5 shadow-sm sm:p-6"
									>
										<div className="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
											<div className="flex min-w-0 items-start gap-4">
												<div className="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-purple-50 text-purple-700">
													<Icon icon="comments" />
												</div>
												<div className="min-w-0">
													<div className="flex flex-wrap items-center gap-2">
														<h3 className="font-semibold text-gray-900">{category.title}</h3>
														<Badge colour={category.is_choir_wide ? 'bg-purple-100 text-purple-800' : 'bg-gray-100'}>
															{category.is_choir_wide ? 'Choir-wide' : 'Ensemble'}
														</Badge>
													</div>
													<p className="mt-1 text-sm text-gray-600">
														{category.is_choir_wide
															? 'Visible to all active choir members'
															: category.ensembles.length > 0
																? `Visible to ${category.ensembles
																	.map(ensemble => ensemble.name)
																	.join(', ')}`
																: 'Restricted to ensembles that are no longer available'}
													</p>
												</div>
											</div>
											<div className="flex shrink-0 flex-wrap items-center gap-2 sm:justify-end">
												{category.can_view_topics && (
													<Button
														href={route('forum.categories.topics.index', { forum_category: category.id })}
														intent="primary"
														weight="outline"
													>
														View topics
														<Icon icon="arrow-right" />
													</Button>
												)}
												{canManageCategories && (
													<>
														<Button
															intent="secondary"
															weight="clear"
															size="sm"
															onClick={() => setActiveForm(category)}
														>
															<Icon icon="pencil" /> Edit
														</Button>
														<Button
															intent="danger"
															weight="clear"
															size="sm"
															onClick={() => deleteCategory(category)}
														>
															<Icon icon="trash" /> Delete
														</Button>
													</>
												)}
											</div>
									</div>
									<div className="mt-5">
										<TopicList
											topics={category.topics}
											showCategories={false}
											emptyTitle="No topics in this category yet"
											emptyDescription="There are no topics in this category yet."
										/>
									</div>
								</article>
								))}
							</div>
						) : (
							<div className="rounded-lg border border-gray-200 bg-white shadow-sm">
								<EmptyState
									title="No categories available"
									description="There are no categories available to you yet."
									icon="comments"
								/>
							</div>
						)}
					</section>
				</div>
			</ForumPageLayout>
		</>
	);
};

const CategoryForm = ({ category, ensembles, onCancel }) => {
	const { route } = useRoute();
	const { data, setData, post, put, processing, errors } = useForm({
		title: category?.title ?? '',
		is_choir_wide: category?.is_choir_wide ?? true,
		ensemble_ids: category?.ensembles?.map(ensemble => ensemble.id) ?? [],
	});

	const submit = event => {
		event.preventDefault();
		const options = { preserveScroll: true, onSuccess: onCancel };

		if (category) {
			put(route('forum.categories.update', { forum_category: category.id }), options);
			return;
		}

		post(route('forum.categories.store'), options);
	};

	return (
		<form onSubmit={submit} className="space-y-5 rounded-lg border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
			<h2 className="text-lg font-semibold text-gray-900">
				{category ? 'Edit category' : 'Create category'}
			</h2>
			<div>
				<label htmlFor="forum-category-title" className="mb-1 block text-sm font-medium text-gray-700">
					Category name
				</label>
				<input
					id="forum-category-title"
					name="title"
					value={data.title}
					onChange={event => setData('title', event.target.value)}
					className="block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
					maxLength={255}
					required
				/>
				{errors.title && <p className="mt-1 text-sm text-red-700">{errors.title}</p>}
			</div>
			<div>
				<label className="mb-2 flex items-center gap-2 text-sm font-medium text-gray-700">
					<input
						type="checkbox"
						checked={data.is_choir_wide}
						onChange={event => setData('is_choir_wide', event.target.checked)}
						className="rounded border-gray-300 text-purple-700 focus:ring-purple-500"
					/>
					Available to all active choir members
				</label>
				<select
					id="forum-category-ensembles"
					name="ensemble_ids"
					multiple
					value={data.ensemble_ids.map(String)}
					disabled={data.is_choir_wide}
					onChange={event =>
						setData(
							'ensemble_ids',
							Array.from(event.target.selectedOptions, option => Number(option.value))
						)
					}
					className="block min-h-32 w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 disabled:bg-gray-100"
				>
					{ensembles.map(ensemble => (
						<option key={ensemble.id} value={ensemble.id}>
							{ensemble.name}
						</option>
					))}
				</select>
				<p className="mt-1 text-xs text-gray-500">
					Uncheck the box to restrict this category to selected ensembles.
				</p>
				{(errors.ensemble_ids || errors['ensemble_ids.0']) && (
					<p className="mt-1 text-sm text-red-700">{errors.ensemble_ids || errors['ensemble_ids.0']}</p>
				)}
			</div>
			<div className="flex justify-end gap-2">
				<Button weight="clear" onClick={onCancel}>
					Cancel
				</Button>
				<Button type="submit" intent="primary" disabled={processing}>
					{processing ? 'Saving…' : 'Save category'}
				</Button>
			</div>
		</form>
	);
};

Index.layout = page => <TenantLayout children={page} />;

export default Index;
