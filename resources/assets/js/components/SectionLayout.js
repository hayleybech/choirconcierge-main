import React from 'react';
import { Tab } from '@headlessui/react';
import { useMediaQuery } from 'react-responsive';
import classNames from '../classNames';

const SectionHeader = ({ section, mobile = false }) => {
	if (!section.title || (mobile ? !section.action : section.hideTitleOnDesktop)) {
		return null;
	}

	return (
		<div className={`${mobile ? 'px-4' : 'px-8'} py-2 bg-gray-100 border-b border-gray-200 flex gap-2 justify-between`}>
			<h3 className="font-semibold text-gray-700">{section.title}</h3>
			{section.action}
		</div>
	);
};

const SectionLayout = ({ sections, layout }) => {
	const isDesktop = useMediaQuery({ query: '(min-width: 1024px)' });
	const visibleSections = sections.filter(section => section.show !== false);
	const mobileSections = visibleSections.filter(section => section.showOnMobile !== false);

	if (isDesktop) {
		return (
			<div className={layout.className}>
				{layout.columns.map((column, index) => (
					<div className={column.className} key={column.id ?? index}>
						{visibleSections
							.filter(section => section.column === (column.id ?? index))
							.map(section => (
								<div key={section.id}>
									<SectionHeader section={section} />
									{section.content}
								</div>
							))}
					</div>
				))}
			</div>
		);
	}

	return (
		<div>
			<Tab.Group>
				<Tab.List className="flex overflow-x-auto border-b border-gray-200">
					{mobileSections.map(section => (
						<Tab
							key={section.id}
							className={({ selected }) =>
								classNames(
									selected
										? 'border-purple-500 text-purple-600'
										: 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700',
									'flex-1 whitespace-nowrap border-b-2 px-3 py-3 text-center text-sm font-medium'
								)
							}
						>
							{section.title}
						</Tab>
					))}
				</Tab.List>

				<Tab.Panels>
					{mobileSections.map(section => (
						<Tab.Panel key={section.id}>
							<SectionHeader section={section} mobile />
							{section.content}
						</Tab.Panel>
					))}
				</Tab.Panels>
			</Tab.Group>
		</div>
	);
};

export default SectionLayout;
