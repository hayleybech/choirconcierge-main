import DateInput from "../resources/assets/js/components/inputs/DateInput";

export default {
    title: 'Input/DateInput',
    component: DateInput,
    argTypes: {
    },
};

const Template = (args, { argTypes }) => ({
    props: Object.keys(argTypes),
    components: { DateInput },
    template: '<date-input label="Test" output-name="test" />',
});

export const Blah = Template.bind({});
Blah.args = {

};