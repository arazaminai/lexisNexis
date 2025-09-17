import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { BehaviorSubject, Observable } from 'rxjs';
import { environment } from '../../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class DocumentService {
  private host = environment.apiHost; 
  private apiUrl = `${this.host}/api`; 
  
  private refereshSubject = new BehaviorSubject<boolean>(false);
  refresh$: Observable<boolean> = this.refereshSubject.asObservable();

  constructor(private http: HttpClient) {}

  setRefresh(value: boolean) {
    this.refereshSubject.next(value);
  }

  get hostUrl() {
    return this.host;
  }

  // Upload a document
  uploadDocument(file: File): Observable<any> {
    const formData = new FormData();
    formData.append('file', file);
    return this.http.post(`${this.apiUrl}/documents`, formData);
  }

  // List all documents
  listDocuments(): Observable<Document[]> {
    return this.http.get(`${this.apiUrl}/documents/`) as Observable<Document[]>;
  }

    // Delete a document
  deleteDocument(id: number): Observable<any> {
    return this.http.delete(`${this.apiUrl}/documents/?id=${id}`);
  }   

    // View a document
  viewDocument(id: number) {
    // Implement view logic, e.g., open in a new tab
    window.open(`${this.apiUrl}/documents/?id=${id}`, '_blank');
  }

  downloadDocument($filepath: string) {
    // Implement download logic
    window.location.href = `${this.apiUrl}${$filepath}`;
  }

  // Search documents
  searchDocuments(query: string): Observable<any> {
    let params = new HttpParams().set('q', query);
    return this.http.get(`${this.apiUrl}/search/`, { params });
  }
}
