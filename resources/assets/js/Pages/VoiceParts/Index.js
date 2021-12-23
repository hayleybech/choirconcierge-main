import React from 'react'
import Layout from "../../Layouts/Layout";
import PageHeader from "../../components/PageHeader";
import AppHead from "../../components/AppHead";
import VoicePartTableDesktop from "./VoicePartTableDesktop";
import VoicePartTableMobile from "./VoicePartTableMobile";

const Index = ({ parts }) => (
    <>
        <AppHead title="Voice Parts" />
        <PageHeader
            title="Voice Parts"
            icon="users-class"
            breadcrumbs={[
                { name: 'Dashboard', url: route('dash')},
                { name: 'Voice Parts', url: route('voice-parts.index')},
            ]}
            actions={[
                { label: 'Add New', icon: 'plus', url: route('voice-parts.create'), variant: 'primary'},
            ]}
        />

        {/* Desktop Table */}
        <div className="hidden lg:flex flex-col">
            <VoicePartTableDesktop voiceParts={parts} />
        </div>

        {/* Mobile Table */}
        <div className="bg-white shadow block lg:hidden">
            <VoicePartTableMobile voiceParts={parts} />
        </div>
    </>
);

Index.layout = page => <Layout children={page} />

export default Index;