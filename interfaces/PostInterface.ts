import TagInterface from './tag-interface'

export default interface PostInterface {
    id: number;
    user_id: number;
    category_id: number;
    title: string;
    slug: string;
    body: string;
    img: string;
    created_at: string;
    updated_at: string;
    tags: TagInterface[];
}