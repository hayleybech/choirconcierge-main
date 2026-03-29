import React, { useState } from 'react';
import TenantLayout from '../../Layouts/TenantLayout';
import PageHeader from '../../components/PageHeader/PageHeader';
import AppHead from '../../components/AppHead';
import FolderTableDesktop from './FolderTableDesktop';
import FolderTableMobile from './FolderTableMobile';
import DeleteDialog from '../../components/DeleteDialog';
import EmptyState from '../../components/EmptyState';
import IndexContainer from '../../components/IndexContainer';
import useRoute from '../../hooks/useRoute';
import DocumentFilters from './DocumentFilters';
import useSortFilterForm from '../../hooks/useSortFilterForm';
import useFilterPane from '../../hooks/useFilterPane';
import FilterSortPane from '../../components/FilterSortPane';
import Sorts from '../../components/Sorts';

const Index = ({ folders, documents, userEnsemblesCount, ensembles, can }) => {
	const { route } = useRoute();

	const [deletingFolder, setDeletingFolder] = useState(null);
	const [deletingDocument, setDeletingDocument] = useState(null);

	const [showFilters, setShowFilters, filterAction, hasNonDefaultFilters] = useFilterPane();

	const sorts = [
		{ id: 'title', name: 'Title', default: true },
		{ id: 'created_at', name: 'Date Created' },
	];

	const filters = [{ name: 'title', defaultValue: '' }];

	const sortFilterForm = useSortFilterForm('folders.index', filters, sorts);

	return (
		<>
			<AppHead title="Documents" />
			<PageHeader
				title="Documents"
				icon="folders"
				breadcrumbs={[
					{ name: 'Dashboard', url: route('dash') },
					{ name: 'Documents', url: route('folders.index') },
				]}
				actions={[
					{
						label: 'Add Folder',
						icon: 'folder-plus',
						url: route('folders.create'),
						variant: 'primary',
						can: 'create_folder',
					},
					filterAction,
				].filter(action => (action?.can ? can[action.can] : !!action))}
			/>

			<IndexContainer
				showFilters={showFilters}
				filterPane={
					<FilterSortPane
						sorts={<Sorts sorts={sorts} form={sortFilterForm} />}
						filters={<DocumentFilters form={sortFilterForm} />}
						closeFn={() => setShowFilters(false)}
					/>
				}
				tableDesktop={
					<FolderTableDesktop
						folders={folders}
						documents={documents}
						isFiltered={!!hasNonDefaultFilters}
						setDeletingFolder={setDeletingFolder}
						setDeletingDocument={setDeletingDocument}
						permissions={can}
						userEnsemblesCount={userEnsemblesCount}
						sortFilterForm={sortFilterForm}
					/>
				}
				tableMobile={
					<FolderTableMobile
						folders={folders}
						documents={documents}
						setShowFilters={setShowFilters}
						isFiltered={!!hasNonDefaultFilters}
						setDeletingFolder={setDeletingFolder}
						setDeletingDocument={setDeletingDocument}
						permissions={can}
						userEnsemblesCount={userEnsemblesCount}
					/>
				}
				emptyState={
					folders.length === 0 && documents.length === 0 ? (
						<EmptyState
							title={hasNonDefaultFilters ? 'No results found' : 'No folders'}
							description={
								hasNonDefaultFilters
									? "We couldn't find any folders or documents matching your search. "
									: "Looks like you don't have any folders or documents yet. This is a great place to store meeting minutes, your constitution, or other important files."
							}
							actionDescription={
								can['create_folder'] && !hasNonDefaultFilters
									? 'Get started by adding a folder, then upload some documents to the folder.'
									: null
							}
							icon="folders"
							href={can['create_folder'] && !hasNonDefaultFilters ? route('folders.create') : null}
							actionLabel="Add Folder"
							actionIcon="folder-plus"
						/>
					) : null
				}
			/>

			<DeleteDialog
				title="Delete Folder"
				url={deletingFolder ? route('folders.destroy', { folder: deletingFolder }) : '#'}
				isOpen={!!deletingFolder}
				setIsOpen={setDeletingFolder}
			>
				Are you sure you want to delete this folder? All of its documents will be permanently removed from our
				servers forever. This action cannot be undone.
			</DeleteDialog>

			<DeleteDialog
				title="Delete Document"
				url={
					deletingDocument
						? route('folders.documents.destroy', {
								folder: deletingDocument.folder_id,
								document: deletingDocument,
						  })
						: '#'
				}
				isOpen={!!deletingDocument}
				setIsOpen={setDeletingDocument}
			>
				Are you sure you want to delete this document? It will be permanently removed from our servers forever.
				This action cannot be undone.
			</DeleteDialog>
		</>
	);
};

Index.layout = page => <TenantLayout children={page} />;

export default Index;
