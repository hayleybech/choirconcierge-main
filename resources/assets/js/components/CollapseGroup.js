import {Disclosure} from "@headlessui/react";
import CollapseHeader from "./CollapseHeader";
import CollapseTitle from "./CollapseTitle";
import React from "react";

const CollapseGroup = ({ items }) => (
    items.map(({ title, show, defaultOpen, action, content }) => (
    <Disclosure defaultOpen={defaultOpen}>
        {({ open }) => (show && <>
            <CollapseHeader>
                <Disclosure.Button>
                    <CollapseTitle open={open}>{title}</CollapseTitle>
                </Disclosure.Button>

                {action}
            </CollapseHeader>

            <Disclosure.Panel>
                {content}
            </Disclosure.Panel>
        </>)}
    </Disclosure>
    ))
);

export default CollapseGroup;