import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class DocumentService {
  private apiUrl = 'http://localhost:8080/api'; // <-- PHP backend

  constructor(private http: HttpClient) {}

  // Upload a document
  uploadDocument(file: File): Observable<any> {
    const formData = new FormData();
    formData.append('file', file);
    return this.http.post(`${this.apiUrl}/documents`, formData);
  }

  // List all documents
  listDocuments(): Observable<any> {
    return this.http.get(`${this.apiUrl}/documents/`);
  }

    // Delete a document
  deleteDocument(id: number): Observable<any> {
    return this.http.delete(`${this.apiUrl}/documents/?id=${id}`);
  }   

    // View a document
  viewDocument(id: number) {
    // Implement view logic, e.g., open in a new tab
    window.open(`http://localhost:8080/api/documents/?id=${id}`, '_blank');
  }

  downloadDocument($filepath: string) {
    // Implement download logic
    window.location.href = `http://localhost:8080${$filepath}`;
  }

  // Search documents
  searchDocuments(query: string): Observable<any> {
    let params = new HttpParams().set('q', query);
    return this.http.get(`${this.apiUrl}/search/`, { params });
  }
}
