import React from 'react';
import { Tab } from '@headlessui/react';
import { useMediaQuery } from 'react-responsive';
import CollapseGroup from '../../components/CollapseGroup';
import classNames from '../../classNames';

const SectionLayout = ({
	columns,
	gridClassName = 'grid-cols-1 divide-y divide-gray-300 sm:grid sm:grid-cols-3 sm:divide-x sm:divide-y-0 xl:grid-cols-4',
	columnClassNames = [
		'sm:col-span-2 xl:col-span-3 divide-y divide-gray-300',
		'sm:col-span-1 divide-y divide-gray-300',
	],
}) => {
	const isDesktop = useMediaQuery({ query: '(min-width: 1024px)' });
	const visibleColumns = columns.map(column => column.filter(section => section.show !== false));
	const visibleSections = visibleColumns.flat().filter(section => section.showOnMobile !== false);

	return isDesktop ? (
		<div className={gridClassName}>
				{visibleColumns.map((column, index) => (
					<div className={columnClassNames[index] ?? 'sm:col-span-1 divide-y divide-gray-300'} key={index}>
						{column.map(section => (
							<div key={section.key ?? section.title}>
								{section.title && !section.hideTitleOnDesktop && (
									<div className="py-2 px-8 bg-gray-100 border-b border-gray-200 flex gap-2 justify-between">
										<h3 className="font-semibold text-gray-700">{section.title}</h3>
										{section.action}
									</div>
								)}
								{section.content}
							</div>
						))}
					</div>
				))}
		</div>
	) : (
		<div>
				<Tab.Group>
					<Tab.List className="flex overflow-x-auto border-b border-gray-200">
						{visibleSections.map(({ title }, index) => (
							<Tab
								key={index}
								className={({ selected }) =>
									classNames(
										selected
											? 'border-purple-500 text-purple-600'
											: 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700',
										'flex-1 whitespace-nowrap border-b-2 px-3 py-3 text-center text-sm font-medium'
									)
								}
							>
								{title}
							</Tab>
						))}
					</Tab.List>

					<Tab.Panels>
						{visibleSections.map((section, index) => (
							<Tab.Panel key={index}>
								{section.action && section.title && (
									<div className="py-2 px-4 bg-gray-100 border-b border-gray-200 flex gap-2 justify-between">
										<h3 className="font-semibold text-gray-700">{section.title}</h3>
										{section.action}
									</div>
								)}
								{section.content}
							</Tab.Panel>
						))}
					</Tab.Panels>
				</Tab.Group>
		</div>
	);
};

export default SectionLayout;
