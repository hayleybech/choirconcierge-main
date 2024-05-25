import React from 'react';
import {useForm} from "@inertiajs/react";
import FormSection from "../FormSection";
import Label from "../inputs/Label";
import FileInput from "../inputs/FileInput";
import Error from "../inputs/Error";
import Button from "../inputs/Button";
import Icon from "../Icon";
import RadioGroup from "../inputs/RadioGroup";
import AttachmentType from "../../AttachmentType";
import TextInput from "../inputs/TextInput";
import useRoute from "../../hooks/useRoute";
import CollapseHeader from "../CollapseHeader";
import { Disclosure } from "@headlessui/react";
import CollapseTitle from "../CollapseTitle";

const SongAttachmentForm = ({ song }) => {
    const { route } = useRoute();

    const { data, setData, post, processing, errors, reset } = useForm({
        attachment_uploads: [],
        type: Object.keys(AttachmentType.types)[0],
        url: '',
        title: '',
    });

    function submit(e) {
        e.preventDefault();
        post(route('songs.attachments.store', {song: song.id}));

        reset();
    }

    return (
      <Disclosure >
        {({ open }) => (<>
          <CollapseHeader>
            <Disclosure.Button>
              <CollapseTitle open={open}>Add Attachment</CollapseTitle>
            </Disclosure.Button>
          </CollapseHeader>

          <Disclosure.Panel>
            <div className="md:max-w-5xl md:mx-auto px-4 sm:px-6 lg:px-8 pb-8">
                <FormSection>
                  <div className="sm:col-span-6">
                    <RadioGroup
                      label={<Label label="Attachment Type" />}
                      options={Object.keys(AttachmentType.types).map(slug => ({
                        id: slug,
                        name: AttachmentType.get(slug).title,
                        textColour: AttachmentType.get(slug).textColour,
                        colour: AttachmentType.get(slug).textColour,
                        icon: AttachmentType.get(slug).icon,
                      }))}
                      vertical
                      selected={data.type}
                      setSelected={value => setData('type', value)}
                    />
                    {errors.type && <Error>{errors.type}</Error>}
                  </div>
                  {data.type === 'youtube' ? (
                    <>
                      <div className="sm:col-span-6">
                        <Label label="YouTube URL" forInput="url" />
                        <TextInput
                          name="url"
                          value={data.url}
                          updateFn={value => setData('url', value)}
                          hasErrors={ !! errors['url'] }
                          placeholder="https://www.youtube.com/watch?v=dQw4w9WgXcQ"
                        />
                        {errors.url && <Error>{errors.url}</Error>}
                      </div>
                      <div className="sm:col-span-6">
                        <Label label="Video description" forInput="title" />
                        <TextInput
                          name="title"
                          value={data.title}
                          updateFn={value => setData('title', value)}
                          hasErrors={ !! errors['title'] }
                          placeholder="Never Gonna Give You Up"
                        />
                        {errors.title && <Error>{errors.title}</Error>}
                      </div>
                    </>
                  ) : (
                    <div className="sm:col-span-6">
                      <Label label="File Upload" forInput="attachment_uploads" />
                      <FileInput
                        name="attachment_uploads"
                        value={data.attachment_uploads}
                        updateFn={value => setData('attachment_uploads', value)}
                        hasErrors={ !! errors['attachment_uploads'] }
                        multiple
                        vertical
                      />
                      {errors.attachment_uploads && <Error>{errors.attachment_uploads}</Error>}
                    </div>
                  )}
                  <div className="">
                    <Button onClick={submit} variant="primary" size="sm" disabled={processing}><Icon icon="check" />Save</Button>
                  </div>
                </FormSection>
            </div>
          </Disclosure.Panel>
        </>)}
      </Disclosure>
    );
}

export default SongAttachmentForm;