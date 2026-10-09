import React from 'react';
import TenantLayout from '../../Layouts/TenantLayout';
import AppHead from '../../components/AppHead';
import Button from '../../components/inputs/Button';
import ButtonV2 from '../../components/inputs/ButtonV2';
import Panel from '../../components/Panel';
import PageTopBar, { PageTopNavigation } from '../../components/PageTopBar';
import { PageHeader, PageHeaderContent, PageHeaderTitle } from '../../components/PageHeader/PageHeader';
import Icon from '../../components/Icon';

const variantGroups = [
	{
		weight: 'Solid',
		variants: ['primary', 'secondary', 'dark', 'danger-solid', 'warning-solid', 'success-solid'],
	},
	{
		weight: 'Outline',
		variants: ['danger-outline', 'warning-outline', 'success-outline'],
	},
	{
		weight: 'Clear',
		variants: ['clear', 'clear-v2', 'clear-inverse', 'danger-clear'],
	},
];

const sizes = ['md', 'sm', 'xs'];
const v2Colors = ['primary', 'secondary', 'danger', 'warning', 'success'];
const v2Weights = ['solid', 'outline', 'clear'];
const v2Sizes = ['lg', 'md', 'sm'];
const v2Themes = ['light', 'dark'];

const Index = ({ setSidebarOpen }) => (
	<>
		<AppHead title="Buttons" />
		<PageTopBar setSidebarOpen={setSidebarOpen}>
			<PageTopNavigation title="Buttons" />
		</PageTopBar>
		<PageHeader>
			<PageHeaderContent>
				<PageHeaderTitle>Buttons</PageHeaderTitle>
			</PageHeaderContent>
		</PageHeader>

		<div className="py-6">
			<div className="mx-auto px-4 sm:px-6 lg:px-16">
				<Panel>
					<div className="flex flex-col gap-y-8">
						<div>
							<h2 className="text-lg font-semibold text-gray-900">Button v1</h2>
							<p className="mt-1 text-sm text-gray-500">
								The shared Button component in every available combination.
							</p>
						</div>

						{variantGroups.map(({ weight, variants }) => (
							<div key={weight} className="flex flex-col gap-y-4">
								<h3 className="text-sm font-semibold text-gray-700">{weight}</h3>
								<div className="flex justify-between">
									{variants.map(variant => (
										<div key={variant} className="overflow-x-auto">
											<h4 className="mb-3 text-sm font-medium capitalize text-gray-600">
												{variant}
											</h4>
											<div className="flex flex-col flex-wrap items-center gap-3">
												{sizes.map(size => (
													<Button
														key={`${variant}-${size}`}
														variant={variant}
														size={size}
														type="button"
													>
														<Icon icon="plus" />
														Button {size}
													</Button>
												))}
											</div>
										</div>
									))}
								</div>
							</div>
						))}

						<div className="flex flex-wrap items-center gap-3 border-t border-gray-200 pt-6">
							<span className="text-sm font-medium text-gray-700">Disabled</span>
							<Button variant="primary" disabled>
								Primary
							</Button>
							<Button variant="secondary" disabled>
								Secondary
							</Button>
							<Button variant="danger-solid" disabled>
								Danger
							</Button>
						</div>
					</div>
				</Panel>
				<Panel>
					<div className="flex flex-col gap-y-8">
						<div>
							<h2 className="text-lg font-semibold text-gray-900">Button v2</h2>
							<p className="mt-1 text-sm text-gray-500">
								Colors, weights, and sizes available in the new button API.
							</p>
						</div>
						{v2Themes.map(theme => (
							<div key={theme} className={theme === 'dark' ? 'rounded-lg bg-gray-800 p-6' : ''}>
								<h3 className={`mb-3 text-sm font-semibold capitalize ${theme === 'dark' ? 'text-gray-100' : 'text-gray-700'}`}>
									{theme} theme
								</h3>
								{v2Weights.map(weight => (
									<div key={weight} className="overflow-x-auto">
										<h3 className={`mb-3 text-sm font-semibold capitalize ${theme === 'dark' ? 'text-gray-100' : 'text-gray-700'}`}>
											{weight}
										</h3>
										<div className="flex justify-between flex-wrap items-center gap-3">
											{v2Colors.map(color => (
												<div key={color} className="flex flex-col gap-3">
													{v2Sizes.map(size => (
														<ButtonV2
															key={`${color}-${size}`}
															color={color}
															weight={weight}
															size={size}
															theme={theme}
															type="button"
														>
															<Icon icon="plus" />
															{color} {size}
														</ButtonV2>
													))}
												</div>
											))}
										</div>
									</div>
								))}
							</div>
						))}
					</div>
				</Panel>
			</div>
		</div>
	</>
);

Index.layout = page => <TenantLayout children={page} />;

export default Index;
