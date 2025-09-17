interface SearchResult {
  id: number;
  filename: string;
  filepath: string;
  filetype: string;
  uploaded_at: string;
  relevance: number;
  highlight: string;
}

export type { SearchResult };