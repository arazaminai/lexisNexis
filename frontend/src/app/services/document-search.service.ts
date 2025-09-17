import { Injectable } from '@angular/core';
import { BehaviorSubject, Observable } from 'rxjs';
import { SearchResult } from '../models/documents';


@Injectable({
  providedIn: 'root'
})
export class DocumentSearchService {
  private resultsSubject = new BehaviorSubject<SearchResult[] | null>([]);
  results$: Observable<SearchResult[] | null> = this.resultsSubject.asObservable();

  constructor() {}

  setResults(results: SearchResult[] | null) {
    this.resultsSubject.next(results);
  }

  clearResults() {
    this.resultsSubject.next(null);
  }
}
