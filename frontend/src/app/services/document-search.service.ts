import { Injectable } from '@angular/core';
import { BehaviorSubject, Observable } from 'rxjs';
import { Document } from '../models/documents';


@Injectable({
  providedIn: 'root'
})
export class DocumentSearchService {
  private resultsSubject = new BehaviorSubject<Document[] | null>([]);
  results$: Observable<Document[] | null> = this.resultsSubject.asObservable();

  constructor() {}

  setResults(results: Document[] | null) {
    this.resultsSubject.next(results);
  }

  clearResults() {
    this.resultsSubject.next(null);
  }
}
