import React from 'react';
import AppHead from '../../components/AppHead';
import CentralLayout from '../../Layouts/CentralLayout';
import Panel from '../../components/Panel';
import classNames from '../../classNames';
import Icon from "../../components/Icon";

const Changelog = ({ logs }) => (
	<>
		<AppHead title="Changelog" />
		<div className="py-6">
			<div className="mx-auto px-4 sm:px-6 lg:px-16">
				<div className="mb-8">
					<h1 className="text-2xl font-semibold text-gray-900 mb-2">
						<Icon icon="code-merge" mr /> Changelog
					</h1>
				</div>

				<Panel>
					<ul role="list" className="space-y-6">
						{logs.map(({ date, heading, content }, index) => (
							<li key={date} className="relative flex gap-x-4">
								<div
									className={classNames(
										index === logs.length - 1 ? 'h-6' : '-bottom-6',
										'absolute top-0 left-0 flex w-6 justify-center'
									)}
								>
									<div className="w-px bg-gray-200" />
								</div>

								<div className="relative flex h-6 w-6 flex-none items-center justify-center bg-white">
									<div className="h-1.5 w-1.5 rounded-full bg-purple-100 ring ring-purple-300" />
								</div>
								<div className="flex-auto">
									<h2 className="font-bold text-gray-900 text-lg">{heading}</h2>
									<div
										dangerouslySetInnerHTML={{ __html: content }}
										className="text-gray-500 text-sm [&>p]:mb-2 [&>ul]:ml-5 [&>ul]:list-disc [&_li]:mb-1 [&_h2]:font-bold [&_h2]:text-lg [&_h2]:mt-5 [&_h2]:text-gray-900 [&_h3]:text-gray-700 [&_h3]:mt-3 [&_h3]:font-bold"
									/>
								</div>
								<time dateTime={date} className="flex-none py-0.5 text-sm text-gray-500">
									{date}
								</time>
							</li>
						))}
					</ul>
				</Panel>
			</div>
		</div>
	</>
);

Changelog.layout = page => <CentralLayout children={page} />;

export default Changelog;
