import { ComponentFixture, TestBed } from '@angular/core/testing';
import { UploadDocumentComponent } from './upload-document.component';
import { HttpClientTestingModule, HttpTestingController } from '@angular/common/http/testing';
import { By } from '@angular/platform-browser';

describe('UploadDocumentComponent', () => {
  let component: UploadDocumentComponent;
  let fixture: ComponentFixture<UploadDocumentComponent>;
  let httpMock: HttpTestingController;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [UploadDocumentComponent, HttpClientTestingModule]
    }).compileComponents();

    fixture = TestBed.createComponent(UploadDocumentComponent);
    component = fixture.componentInstance;
    httpMock = TestBed.inject(HttpTestingController);
    fixture.detectChanges();
  });

  afterEach(() => {
    httpMock.verify();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });

  it('should set selectedFile when a file is selected', () => {
    const file = new File(['dummy'], 'test.pdf', { type: 'application/pdf' });
    const event = { target: { files: [file] } } as any;
    component.onFileSelected(event);
    expect(component.selectedFile).toBe(file);
    expect(component.uploadStatus).toBe('');
  });

  it('should set dragOver true on drag over', () => {
    const event = new DragEvent('dragover');
    component.onDragOver(event);
    expect(component.dragOver).toBeTrue();
  });

  it('should set dragOver false on drag leave', () => {
    const event = new DragEvent('dragleave');
    component.onDragLeave(event);
    expect(component.dragOver).toBeFalse();
  });

  it('should set selectedFile on drop', () => {
    const file = new File(['dummy'], 'test.txt', { type: 'text/plain' });
    const dataTransfer = new DataTransfer();
    dataTransfer.items.add(file);
    const event = new DragEvent('drop', { dataTransfer });
    component.onDrop(event);
    expect(component.selectedFile?.name).toBe('test.txt');
    expect(component.uploadStatus).toBe('');
    expect(component.dragOver).toBeFalse();
  });

  it('should show error if upload is clicked without a file', () => {
    component.selectedFile = null;
    component.onUpload();
    expect(component.uploadStatus).toContain('Please select a file');
  });

  it('should POST file and emit uploadComplete on success', () => {
    const file = new File(['dummy'], 'test.pdf', { type: 'application/pdf' });
    component.selectedFile = file;
    spyOn(component.uploadComplete, 'emit');

    component.onUpload();

    const req = httpMock.expectOne('http://localhost:8080/api/documents/');
    expect(req.request.method).toBe('POST');
    req.flush({}); // Simulate success

    expect(component.selectedFile).toBeNull();
    expect(component.uploadComplete.emit).toHaveBeenCalled();
  });

  it('should set uploadStatus on upload error', () => {
    const file = new File(['dummy'], 'fail.pdf', { type: 'application/pdf' });
    component.selectedFile = file;
    component.onUpload();

    const req = httpMock.expectOne('http://localhost:8080/api/documents/');
    req.error(new ErrorEvent('Upload failed'));

    expect(component.uploadStatus).toContain('Upload failed');
  });
});
