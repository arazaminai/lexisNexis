interface Document {
  id: number;
  filename: string;
  filepath: string;
  filetype: string;
  uploaded_at: string;
  relevance?: number;
  highlight?: string;
}

interface ViewDocument extends Document {
  host: string;
}
export type { Document, ViewDocument };