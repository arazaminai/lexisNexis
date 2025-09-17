import { Injectable } from '@angular/core';
import { BehaviorSubject, Observable } from 'rxjs';

export interface SearchResult {
  id: number;
  filename: string;
  filepath: string;
  filetype: string;
  uploaded_at: string;
  relevance: number;
  highlight: string;
}

@Injectable({
  providedIn: 'root'
})
export class DocumentSearchService {
  private resultsSubject = new BehaviorSubject<SearchResult[] | null>([]);
  results$: Observable<SearchResult[] | null> = this.resultsSubject.asObservable();

  constructor() {}

  // Update the results (emit to all subscribers)
  setResults(results: SearchResult[] | null) {
    this.resultsSubject.next(results);
  }

  // Optional: clear results
  clearResults() {
    this.resultsSubject.next(null);
  }
}
